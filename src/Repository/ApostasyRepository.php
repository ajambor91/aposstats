<?php

namespace App\Repository;

use App\Entity\Apostasy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apostasy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apostasy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apostasy[]    findAll()
 * @method Apostasy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApostasyRepository extends ServiceEntityRepository
{

    private $firstCondition = true;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apostasy::class);
    }

    public function saveApostasy(array $data): bool
    {
        $em = $this->getEntityManager();
        foreach ($data as $datum) {
            do {
                $apostasyEntity = new Apostasy();
                $apostasyEntity->setOrdinalNumber($datum['ordinal_number'])
                    ->setCity($datum['city'])
                    ->setApostasyYear((int)$datum['apostasy_year'])
                    ->setHash($datum['hash'])
                    ->setScrappedAt($datum['scrapped_at'])
                    ->setFittedCity($datum['fittedCity'])
                    ->setFittedVoivdeship($datum['fittedVoivodeship']);
                try {
                    $em->persist($apostasyEntity);
                    $em->flush();
                } catch (\Exception $e) {
                    $em = $this->getEntityManager();
                    $em = $em->create(
                        $em->getConnection(),
                        $em->getConfiguration()
                    );
                }
            } while (!$em->isOpen());
        }
        return true;
    }

    public function getApostasiesData(array $data): array
    {
        $qb = $this->createQueryBuilder('q');
        if (isset($data['from']) || isset($data['to'])) {
            $periodArr = $this->getPeriod(
                isset($data['from']) ?: null,
                isset($data['to']) ?: null
            );
            $qb = $this->setPeriod($qb, $periodArr);
        }
        if (isset($data['cityId'])) {
            $qb = $this->setCity($qb, $data);
        }

        if (isset($data['voivodeshipId'])) {
            $qb = $this->setVoivodeship($qb, $data);
        }
        return $qb->getQuery()->getResult();
    }

    public function getApostatesStatistics(array $data, int $periodType): array
    {

        $from = isset($data['from']) ? $data['from'] : null;
        $to = isset($data['to']) ? $data['to'] : null;
        $interval = $this->getPeriodInterval($from, $to, $periodType);
        $formats = [
            Apostasy::BY_YEAR => 'Y',
            Apostasy::BY_MONTH => 'Y-m',
            Apostasy::BY_DAY => 'Y-m-d'
        ];
        $result = [];
        /**
         * @var \DateTime $dt
         */
        foreach ($interval as $dt) {
            $qb = $this->createQueryBuilder('q');
            $qb->select("count('*')");
            $this->setDate($qb, $dt, $periodType);
            if (isset($data['cityId'])) {
                $qb = $this->setCity($qb, $data);
            }

            if (isset($data['voivodeshipId'])) {
                $qb = $this->setVoivodeship($qb, $data);
            }
            try {
                $result[] = [
                    'name' => $dt->format($formats[$periodType]),
                    'value' => $qb->getQuery()->getSingleResult()[1]
                ];
            } catch (NoResultException $e) {
            } catch (NonUniqueResultException $e) {
            }
        }
        return $result;

    }

    public function getFirstApostasy(int $periodType): string
    {
        $qb = $this->createQueryBuilder('q');
        if ($periodType === Apostasy::BY_YEAR) {
            $qb->select("MIN(q.apostasyYear)");
        } else {
            $qb->select("MIN(q.scrappedAt)");
        }
        $result = $qb->getQuery()->getSingleResult()[1];;
        return $result;
    }

    private function getPeriodInterval($from, $to, int $periodType): \DatePeriod
    {
        $intervals = [
            Apostasy::BY_YEAR => '1 year',
            Apostasy::BY_MONTH => '1 month',
            Apostasy::BY_DAY => '1 day'
        ];
        $today = new \DateTime();
        $startDate = new \DateTime($this->getFirstApostasy($periodType));
        $from = $from != null ? new \DateTime($from) : $startDate;
        $to = $to != null  ? new \DateTime($to) : $today;
        $from = $from < $startDate ? $startDate : $from;
        $to = $to > $today ? $today : $to;
        $period = $this->setIntervalPeriod($from, $to, $periodType);
        $interval = \DateInterval::createFromDateString($intervals[$periodType]);
        $period = new \DatePeriod($period['from'], $interval, $period['to']);
        return $period;

    }

    private function setIntervalPeriod(\DateTime $begin, \DateTime $end, int $periodType): array
    {

        if ($periodType === Apostasy::BY_YEAR) {
            $begin->modify('first day of january');
            $end->modify('last day of december');
        } elseif ($periodType === Apostasy::BY_MONTH) {
            $begin->modify('first day of this month');
            $end->modify('last day of this month');
        }
        return [
            'from' => $begin,
            'to' => $end
        ];
    }

    private function setDate(QueryBuilder $builder, \DateTime $date, int $periodType): QueryBuilder
    {
        $year = $date->format('Y');
        if ($periodType === Apostasy::BY_YEAR) {
            $builder->where('q.apostasyYear = :apostasyYear')
                ->setParameter('apostasyYear', $year);
        } elseif ($periodType === Apostasy::BY_MONTH) {
            $from = clone $date;
            $to = clone $date;
            $date = null;
            unset($date);
            $builder->where('q.scrappedAt > :from')
                ->andWhere('q.scrappedAt < :to')
                ->andWhere('q.apostasyYear = :apostasyYear')
                ->setParameters([
                    'apostasyYear' => $year,
                    'from' => $from->modify('first day of this month')->setTime(0,0,0),
                    'to' => $to->modify('last day of this month')->setTime(23,59,59)
                ]);
        } elseif ($periodType === Apostasy::BY_DAY) {
            $from = clone $date;
            $to = clone $date;
            $date = null;
            unset($date);
            $builder->where('q.scrappedAt > :from')
                ->andWhere('q.scrappedAt < :to')
                ->andWhere('q.apostasyYear = :apostasyYear')
                ->setParameters([
                    'apostasyYear' => $year,
                    'from' => $from->setTime(0,0,0),
                    'to' => $to->setTime(23,59,59)
                ]);
        }
        $this->firstCondition = false;
        return $builder;
    }

    private function getPeriod(string $from, string $to): array
    {
        return [
            'from' => $from != null ? new \DateTime($from) : new \DateTime('00.00.0000'),
            'to' => $to != null ? new \DateTime($to) : new \DateTime()
        ];
    }

    private function setPeriod(QueryBuilder $builder, array $period): QueryBuilder
    {
        $builder->where('q.scrappedAt > :from q.scrappedAt < :to')
            ->setParameters([
                'from' => $period['from'],
                'to' => $period['to']
            ]);
        $this->firstCondition = false;
        return $builder;
    }

    private function setCity(QueryBuilder $builder, $data): QueryBuilder
    {
        $where = 'q.fittedCity = :cityId';
        if ($this->firstCondition) {
            $builder->where($where);
        } else {
            $builder->andWhere($where);
        }
        $this->firstCondition = false;
        $builder->setParameter('cityId', $data['cityId']);
        return $builder;
    }

    private function setVoivodeship(QueryBuilder $builder, $data): QueryBuilder
    {
        $where = 'q.fittedVoivdeship = :voivodeshipId';
        if ($this->firstCondition) {
            $builder->where($where);
        } else {
            $builder->andWhere($where);
        }
        $builder->setParameter('voivodeshipId', $data['voivodeshipId']);
        $this->firstCondition = false;

        return $builder;
    }


    // /**
    //  * @return Apostasy[] Returns an array of Apostasy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Apostasy
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
