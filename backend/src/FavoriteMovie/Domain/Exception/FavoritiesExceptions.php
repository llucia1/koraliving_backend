<?php
declare(strict_types=1);
namespace App\FavoriteMovie\Domain\Exception;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;



final class FavoritiesExceptions {//NOSONAR

    public function __invoke(\exception $e):JsonResponse{
                return $this->handleException($e);
     }


    protected function handleException(\Exception $e): JsonResponse
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Unexpected error occurred.';
        
        switch (true) {
            
            case $e instanceof FavoritiesNotFound:
            case $e instanceof FavoritiesEmptyException:
                $status = Response::HTTP_NOT_FOUND;
                $message = $e->getMessage();
                break;
            case $e instanceof InvalidArgumentException:
                $status = Response::HTTP_BAD_REQUEST;
                $message = $e->getMessage();
                break;
            
            case $e instanceof FavoritiesDuplicatedException:
                
                $status = Response::HTTP_CONFLICT;
                $message = $e->getMessage();
                break;
    
            case $e instanceof HttpException:
                $status = $e->getStatusCode();
                $message = $e->getMessage();
                break;
    
            default:
                break;
        }
    
        return new JsonResponse(['error' => $message], $status);
    }
}