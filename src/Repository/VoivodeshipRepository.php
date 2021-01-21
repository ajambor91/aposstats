<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Voivodeship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voivodeship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voivodeship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voivodeship[]    findAll()
 * @method Voivodeship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoivodeshipRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voivodeship::class);
    }

    public function insertData(array $voivodeships): bool {
        $em = $this->getEntityManager();
        $voivodeshipsCount = count($voivodeships);
        for ($i = 0; $i < $voivodeshipsCount; $i++) {
            $voivodeshipEntity = new Voivodeship();
            $voivodeshipEntity->setName($voivodeships[$i]['voivodeship']);
            $citiesCount = count($voivodeships[$i]['cities']);
            for ($j = 0; $j < $citiesCount; $j++) {
                $cityEntity = new City();
                $cityEntity->setName($voivodeships[$i]['cities'][$j]);
                $voivodeshipEntity->addCity($cityEntity);
                unset($voivodeships[$i]['cities'][$j]);
            }
            try {
                $em->clear();
                $em->persist($voivodeshipEntity);
                $em->flush();
                unset($voivodeships[$i]);
            }
            catch (\Exception $exception){
                dump($exception);die;
                return false;
            }
        }
        return true;
    }
    // /**
    //  * @return Voivodeship[] Returns an array of Voivodeship objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voivodeship
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
