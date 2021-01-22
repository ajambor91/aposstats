<?php

namespace App\Repository;

use App\Entity\Apostasy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
            $qb = $this->setPerid($qb, $periodArr);
        }
        if (isset($data['cityId'])) {
            $qb = $this->setCity($qb, $data);
        }

        if (isset($data['voivodeshipId'])) {
            $qb = $this->setCity($qb, $data);
        }
        return $qb->getQuery()->getResult();
    }

    private function getPeriod(string $from, string $to): array
    {
        return [
            'from' => $from != null ? new \DateTime($from) : new \DateTime('00.00.0000'),
            'to' => $to != null ? new \DateTime($to) : new \DateTime()
        ];
    }

    private function setPerid(QueryBuilder $builder, array $period): QueryBuilder
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

        $builder->setParameter('cityId', $data['cityId']);
        return $builder;
    }

    private function setVoivodeship(QueryBuilder $builder, $data): QueryBuilder
    {
        $where = 'q.fittedVoivodeship = :voivodeshipId';
        if ($this->firstCondition) {
            $builder->where($where);
        } else {
            $builder->andWhere($where);
        }
        $builder->setParameter('voivodeshipId', $data['voivodeshipId']);
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
