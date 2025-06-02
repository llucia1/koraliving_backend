<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601FavoritiesMovies extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create favorite_movies table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE favorite_movies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            imdb_id VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            year VARCHAR(255) NOT NULL,
            poster VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE favorite_movies');
    }
}
