<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Application\Response;

final readonly class MoviesFavoritiesResponses
{
    private array $moviesFavorities;

    public function __construct(MoviesFavoritiesResponse ...$moviesFavorities)
    {
        $this->moviesFavorities = $moviesFavorities;
    }

    public function get(): array
    {
        return $this->moviesFavorities;
    }
}