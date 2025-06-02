<?php

declare(strict_types=1);

namespace App\FavoriteMovie\Presentation\Rest\V1;

use App\FavoriteMovie\Application\Request\CreateFavoriteMovieRequest;
use App\FavoriteMovie\Application\Service\AddFavoriteMovieService;
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
final class PostFavoriteMovieController extends AbstractController
{
    public function __construct(
        private AddFavoriteMovieService $addFavoriteMovieService,
      private readonly  LoggerInterface $logger
    ) {}

    #[Route('/api/favorites', name: 'post_favorite_movie', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (
            !isset($data['imdbId']) ||
            !isset($data['title']) ||
            !isset($data['year']) ||
            !isset($data['poster'])
        ) {
            return new JsonResponse(['error' => 'Missing fields.'], 400);
        }


        try {
            $this->logger->info('Start Post a one movie favorities.');
            
            
            $favoritiesVo = new MoviesFavoritiesVo(
                    (isset($data['imdbId']) ? new ImdbIdVo($data['imdbId']) : null),
                     (isset($data['title'] )? new TitleVo($data['title'])  : null),
                      (isset($data['year']) ? new YearVo($data['year'])  : null),
                    (isset($data['poster']) ? new PosterVo( $data['poster']) : null)
            );

            $result = $this->addFavoriteMovieService->__invoke( $favoritiesVo );

            return new JsonResponse(['message' => 'Favorite movie added. ImdbId = ' . $result], 201);
            
        } catch (\Exception $e) {
            $this->logger->error('Exception:( -> ' . $e->getMessage());
            $vmException = new FavoritiesExceptions();
            return $vmException($e);
        }
    }
}