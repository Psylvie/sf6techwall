<?php

namespace App\Repository;

use App\Entity\EntityAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntityAttachment>
 *
 * @method EntityAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityAttachment[]    findAll()
 * @method EntityAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntityAttachment::class);
    }

    public function findByType(string $type)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
    public function findByEntityId(?int $entityId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.entityId = :entityId')
            ->setParameter('entityId', $entityId)
            ->getQuery()
            ->getResult();
    }

    public function save(EntityAttachment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EntityAttachment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EntityAttachment[] Returns an array of EntityAttachment objects
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

//    public function findOneBySomeField($value): ?EntityAttachment
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
