<?php

declare(strict_types=1);

namespace App\FavoriteMovie\Presentation\Rest\V1;


use App\FavoriteMovie\Application\Service\GetAllFavoriteMovieService;
use App\FavoriteMovie\Application\Response\MoviesFavoritiesResponse;
use App\FavoriteMovie\Domain\Exception\FavoritiesExceptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
final class GetAllFavoriteMovieController extends AbstractController
{
    public function __construct(
        private GetAllFavoriteMovieService $getAllFavoriteMovieService,
      private readonly  LoggerInterface $logger
    ) {}

    #[Route('/api/favorites', name: 'get_all_favorite_movie', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {


        try {
            $this->logger->info('Start Get all movie favorities.');

            $result = $this->getAllFavoriteMovieService->__invoke();

            $moviesArray = array_map(
                fn(MoviesFavoritiesResponse $favorities): array => [
                    'imdbId' => $favorities->imdbId(),
                    'title'  => $favorities->title(),
                    'year'   => $favorities->year(),
                    'poster' => $favorities->poster()
                ],
                $result->get()
            );

            return $this->json($moviesArray, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $vmException = new FavoritiesExceptions();
            return $vmException($e);
        }
    }
}