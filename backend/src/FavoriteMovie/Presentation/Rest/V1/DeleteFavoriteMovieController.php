<?php

declare(strict_types=1);

namespace App\FavoriteMovie\Presentation\Rest\V1;

use App\FavoriteMovie\Application\Request\CreateFavoriteMovieRequest;
use App\FavoriteMovie\Application\Service\AddFavoriteMovieService;
use App\FavoriteMovie\Application\Service\DeleteFavoriteMovieService;
use App\FavoriteMovie\Domain\Exception\FavoritiesExceptions;
use App\FavoriteMovie\Domain\VO\ImdbIdVo;
use App\FavoriteMovie\Domain\VO\MoviesFavoritiesVo;
use App\FavoriteMovie\Domain\VO\PosterVo;
use App\FavoriteMovie\Domain\VO\TitleVo;
use App\FavoriteMovie\Domain\VO\YearVo;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class DeleteFavoriteMovieController extends AbstractController
{
    public function __construct(
        private DeleteFavoriteMovieService $deleteFavoriteMovieService,
      private readonly  LoggerInterface $logger
    ) {}

    #[Route('/api/favorites/{imdbIdRequest}', name: 'delete_favorite_movie', methods: ['DELETE'])]
    public function __invoke(string $imdbIdRequest): JsonResponse
    {
        try {
            $this->logger->info('Start Delete a one movie favorities.');
            
            
            $imdbIdVo = new ImdbIdVo($imdbIdRequest);

            $this->deleteFavoriteMovieService->__invoke( $imdbIdVo );

            return $this->json(null, status: Response::HTTP_NO_CONTENT);
            
        } catch (\Exception $e) {
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $vmException = new FavoritiesExceptions();
            return $vmException($e);
        }
    }
}