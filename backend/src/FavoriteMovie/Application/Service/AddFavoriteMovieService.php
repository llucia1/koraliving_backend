<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Application\Service;

use App\FavoriteMovie\Domain\Exception\FavoritiesDuplicatedException;
use App\FavoriteMovie\Domain\Repository\IFavoriteMovieRepository;
use App\FavoriteMovie\Domain\VO\MoviesFavoritiesVo;
use App\FavoriteMovie\Infrastructure\DB\SQLite\Entity\FavoriteMovieEntity;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AddFavoriteMovieService
{
    public function __construct(
      private readonly  LoggerInterface $logger,
      private readonly IFavoriteMovieRepository $favoriteMovieRepository
    ){

    }

    public function __invoke( MoviesFavoritiesVo $moviesFavoritiesVo ): string
    {
            $this->logger->info("Create a new Movie Favorities.");

        $existingFavoritie = $this->favoriteMovieRepository->findByImdbId($moviesFavoritiesVo->imdbId()->value());
        if ($existingFavoritie) {
            throw new FavoritiesDuplicatedException();
        }



        $favoritieMovieEntity = new FavoriteMovieEntity();
        $favoritieMovieEntity->setImdbId($moviesFavoritiesVo->imdbId()->value());
        $favoritieMovieEntity->setTitle($moviesFavoritiesVo->title()->value());
        $favoritieMovieEntity->setYear($moviesFavoritiesVo->year()->value());
        $favoritieMovieEntity->setPoster($moviesFavoritiesVo->poster()->value());
        $favoritieMovieEntity->setCreatedAt(new \DateTimeImmutable());

        try {
            $this->favoriteMovieRepository->save($favoritieMovieEntity);
        } catch (Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $e->getMessage());
        }
        
        return $moviesFavoritiesVo->imdbId()->value();



    }
}