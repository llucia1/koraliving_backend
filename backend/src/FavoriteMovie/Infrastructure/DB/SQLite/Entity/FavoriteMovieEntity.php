<?php

declare(strict_types=1);

namespace App\FavoriteMovie\Infrastructure\DB\SQLite\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'favorite_movies')]
class FavoriteMovieEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string')]
    private string $year;

    #[ORM\Column(type: 'string')]
    private string $poster;

    #[ORM\Column(type: 'string', unique: true)]
    private string $imdbId;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;
    
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function getPoster(): string
    {
        return $this->poster;
    }

    public function getImdbId(): string
    {
        return $this->imdbId;
    }

public function setId(?int $id): self
{
    $this->id = $id;
    return $this;
}

public function setTitle(string $title): self
{
    $this->title = $title;
    return $this;
}

public function setYear(string $year): self
{
    $this->year = $year;
    return $this;
}

public function setPoster(string $poster): self
{
    $this->poster = $poster;
    return $this;
}

public function setImdbId(string $imdbId): self
{
    $this->imdbId = $imdbId;
    return $this;
}



}