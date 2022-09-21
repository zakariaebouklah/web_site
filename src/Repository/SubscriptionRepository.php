<?php

namespace App\Repository;

use App\Entity\AnnonceFormation;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function add(Subscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param AnnonceFormation $formation
     * @return array
     */
    public function findInstitutions(AnnonceFormation $formation): array
    {
        return $this->createQueryBuilder('i')
            ->select('i.homeInstitution, COUNT(i.homeInstitution) as count')
            ->where('i.annonceFormation = :latestFormation')
            ->setParameter('latestFormation',$formation)
            ->groupBy('i.homeInstitution')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param AnnonceFormation $formation
     * @return array
     */
    public function findAteliers(AnnonceFormation $formation): array
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('count', 'count');

        $sql = 'select a.name as name, count(sa.atelier_id) as count from subscription s 
                    INNER JOIN subscription_atelier sa ON s.id = sa.subscription_id 
                    INNER JOIN atelier a ON a.id = sa.atelier_id 
                    WHERE s.annonce_formation_id = :formation 
                    GROUP BY a.name;';
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('formation', $formation->getId(), Types::INTEGER);

        return $query->getResult();
    }

    /**
     * @param AnnonceFormation $formation
     * @return array
     */
    public function findYears(AnnonceFormation $formation): array
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addScalarResult('inscription_year', 'year');
        $rsm->addScalarResult('count', 'count');

        $sql = 'SELECT s.inscription_year, COUNT(s.inscription_year) as count FROM subscription s 
                    WHERE s.annonce_formation_id = :formation 
                    GROUP BY s.inscription_year;';

        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('formation', $formation->getId(), Types::INTEGER);

        return $query->getResult();
    }



//    /**
//     * @return Subscription[] Returns an array of Subscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subscription
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
