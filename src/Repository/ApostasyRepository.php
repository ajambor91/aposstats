<?php

namespace App\Repository;

use App\Entity\Apostasy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apostasy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apostasy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apostasy[]    findAll()
 * @method Apostasy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApostasyRepository extends ServiceEntityRepository
{
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
            }while(!$em->isOpen());
        }
        return true;
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
