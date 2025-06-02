<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Domain\Repository;

use App\FavoriteMovie\Infrastructure\DB\SQLite\Entity\FavoriteMovieEntity;

interface IFavoriteMovieRepository
{
    public function save(FavoriteMovieEntity $favorite): void;

    public function delete(FavoriteMovieEntity $favorite): void;

    public function findByImdbId(string $imdbId): ?FavoriteMovieEntity;

    public function findAllFavorites(): array;

}