<?php
declare(strict_types=1);

namespace App\FavoriteMovie\Application\Response;

final class MoviesFavoritiesResponse
{
    public function __construct(
        private readonly string $imdbId,
        private readonly string $title,
        private readonly ?string $year,
        private readonly ?string $poster,

    ) {
    }
    public function imdbId(): string
    {
        return $this->imdbId;
    }
    public function title(): string
    {
        return $this->title;
    }
    public function year(): ?string
    {
        return $this->year;
    }
    public function poster(): ?string
    {
        return $this->poster;
    }
    public function toArray(): array
    {
        return [
            'imdbId' => $this->imdbId(),
            'title' => $this->title(),
            'year' => $this->year(),
            'poster' => $this->poster(),
        ];
    }
}