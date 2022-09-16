<?php

namespace App\Repository;

use App\Entity\AnnonceFormation;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

//    public function findInstitutionsStats(AnnonceFormation $formation, string $institution): array
//    {
//        return $this->createQueryBuilder('i')
//                    ->where('i.annonceFormation = :latestFormation')
//                    ->andWhere('i.homeInstitution = :institution')
//                    ->setParameter('latestFormation',$formation)
//                    ->setParameter('institution',$institution)
//                    ->getQuery()
//                    ->getArrayResult()
//            ;
//    }

    public function findAtelierStats(AnnonceFormation $formation, string $atelier): array
    {
        return $this->createQueryBuilder('a')
                    ->where('a.annonceFormation = :latestFormation')
                    ->andWhere(':atelier IN a.ateliersDeFormation')
                    ->setParameter('latestFormation',$formation)
                    ->setParameter('atelier',$atelier)
                    ->getQuery()
                    ->getArrayResult()
            ;
    }

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
