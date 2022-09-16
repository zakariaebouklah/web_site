<?php

namespace App\Repository;

use App\Entity\AnnonceFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnonceFormation>
 *
 * @method AnnonceFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnonceFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnonceFormation[]    findAll()
 * @method AnnonceFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnonceFormation::class);
    }

    public function add(AnnonceFormation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AnnonceFormation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrentFormation(): ?AnnonceFormation
    {
        return $this->createQueryBuilder('af')
            ->where('af.dateDebut <= :curdate')
            ->andWhere('af.dateFin >= :curdate')
            ->setParameter('curdate', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult()
                    ;
    }

    /**
     * @return array<AnnonceFormation>
     */
    public function findAllOldEditions(): array
    {
        return $this->createQueryBuilder('af')
            ->where('af.dateFin < :curdate')
            ->setParameter('curdate', new \DateTimeImmutable())
            ->orderBy('af.dateFin', 'DESC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

//    /**
//     * @return AnnonceFormation[] Returns an array of AnnonceFormation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnnonceFormation
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
