<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Livre;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 *
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function add(Emprunt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Emprunt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Emprunt[] Returns an array of Emprunt objects
    */
    public function findNLast(int $n): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date_emprunt', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Emprunt[] Returns an array of Emprunt objects
    */
    public function findByEmprunteur(Emprunteur $emprunteur): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.emprunteur', 'emp')
            ->andWhere('emp.id = :emprunteurId')
            ->setParameter('emprunteurId', $emprunteur->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Emprunt[] Returns an array of Emprunt objects
    */
    public function findByLivre(Livre $livre): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.livre', 'l')
            ->andWhere('l.id = :livreId')
            ->setParameter('livreId', $livre->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findByDateRetourAvantLe(DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_retour < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findByDateRetourNull(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_retour IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findRetourNull(Livre $livre): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.livre', 'l')
            ->andWhere('e.date_retour IS NULL')
            ->andWhere('l.id = :livreId')
            ->setParameter('livreId', $livre->getId())
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Emprunt[] Returns an array of Emprunt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Emprunt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
