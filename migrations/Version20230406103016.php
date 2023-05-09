<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230406103016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add movies table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE movie (id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, length TIME(0) WITHOUT TIME ZONE NOT NULL, release_date DATE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN movie.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE movie');
    }
}
