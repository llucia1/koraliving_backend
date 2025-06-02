<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Domain\Exception;


class FavoritiesNotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct(sprintf("Movie not found."));
    }
}