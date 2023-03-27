<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function save(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getBestSells(int $nbr): array
    {
        $query = $this->createQueryBuilder('game')
            ->groupBy('game.id')
            ->orderBy('SUM(game.sold)', 'DESC')
            ->setMaxResults($nbr)
            ->getQuery();
        return $query->getResult();
    }

    /*public function orderBySells(): array
    {
        $query = $this->createQueryBuilder('game')
            ->groupBy('game.id')
            ->orderBy('SUM(game.sold)', 'DESC')
            ->getQuery();
        return $query->getResult();
    }

    public function orderByPrice(): array
    {
        $query = $this->createQueryBuilder('game')
            ->groupBy('game.id')
            ->orderBy('game.price', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    public function orderByDate(): array
    {
        $query = $this->createQueryBuilder('game')
            ->groupBy('game.id')
            ->orderBy('game.creationDate', 'DESC')
            ->getQuery();
        return $query->getResult();
    }*/

    public function findByName($name): array
    {
        $query = $this->createQueryBuilder('game')
            ->andWhere('game.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
        return $query->getResult();
    }


//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
