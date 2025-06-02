<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Application\Service;

use App\FavoriteMovie\Application\Response\MoviesFavoritiesResponse;
use App\FavoriteMovie\Application\Response\MoviesFavoritiesResponses;
use App\FavoriteMovie\Domain\Exception\FavoritiesDuplicatedException;
use App\FavoriteMovie\Domain\Exception\FavoritiesEmptyException;
use App\FavoriteMovie\Domain\Repository\IFavoriteMovieRepository;
use App\FavoriteMovie\Domain\VO\MoviesFavoritiesVo;
use App\FavoriteMovie\Infrastructure\DB\SQLite\Entity\FavoriteMovieEntity;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GetAllFavoriteMovieService
{
    public function __construct(
      private readonly  LoggerInterface $logger,
      private readonly IFavoriteMovieRepository $favoriteMovieRepository
    ){

    }

    public function __invoke(  ):MoviesFavoritiesResponses
    {
            $this->logger->info("Create a new Movie Favorities.");

        $existingFavoritie = $this->favoriteMovieRepository->findAllFavorites();

        return empty($existingFavoritie)
            ? throw new FavoritiesEmptyException()
            : $this->toResponse($existingFavoritie);

    }

    public function toResponse(array $favorites): MoviesFavoritiesResponses
    {
        $mapFn = static fn(FavoriteMovieEntity $movie) => new MoviesFavoritiesResponse(
            imdbId: $movie->getImdbId(),
            title: $movie->getTitle(),
            year: $movie->getYear(),
            poster: $movie->getPoster()
        );

        return new MoviesFavoritiesResponses(...array_map($mapFn, $favorites));
    }
}