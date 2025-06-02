<?php

declare(strict_types=1);

namespace App\FavoriteMovie\Infrastructure\DB\SQLite\Repository;


use App\FavoriteMovie\Domain\Repository\IFavoriteMovieRepository;
use App\FavoriteMovie\Infrastructure\DB\SQLite\Entity\FavoriteMovieEntity;
use Doctrine\ORM\EntityManagerInterface;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class FavoriteMovieRepository extends ServiceEntityRepository implements IFavoriteMovieRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteMovieEntity::class);
    }

    public function save(FavoriteMovieEntity $favorite): void
    {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($favorite);
            $entityManager->flush();
    }

    public function delete(FavoriteMovieEntity $favorite): void
    {
        $this->getEntityManager()->remove($favorite);
        $this->getEntityManager()->flush();
    }

    public function findByImdbId(string $imdbId): ?FavoriteMovieEntity
    {
        return $this->findOneBy(['imdbId' => $imdbId]);
    }

    public function findAllFavorites(): array
    {
        return $this->findAll();
    }
}