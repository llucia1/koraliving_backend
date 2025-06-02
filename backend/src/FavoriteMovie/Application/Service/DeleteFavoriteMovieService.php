<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Application\Service;

use App\FavoriteMovie\Domain\Exception\FavoritiesDuplicatedException;
use App\FavoriteMovie\Domain\Exception\FavoritiesNotFound;
use App\FavoriteMovie\Domain\Repository\IFavoriteMovieRepository;
use App\FavoriteMovie\Domain\VO\ImdbIdVo;
use App\FavoriteMovie\Domain\VO\MoviesFavoritiesVo;
use App\FavoriteMovie\Infrastructure\DB\SQLite\Entity\FavoriteMovieEntity;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DeleteFavoriteMovieService
{
    public function __construct(
      private readonly  LoggerInterface $logger,
      private readonly IFavoriteMovieRepository $favoriteMovieRepository
    ){

    }
    public function __invoke( ImdbIdVo $imdbIdVo ): void
    {
        $this->logger->info("Delete Movie Favorities.");

        $favoritieMovie = $this->favoriteMovieRepository->findByImdbId($imdbIdVo->value());
        if (!$favoritieMovie) {
            throw new FavoritiesNotFound();
        }

        try {
            $this->favoriteMovieRepository->delete($favoritieMovie);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }
    }
}