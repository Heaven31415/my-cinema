<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230601140349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change type of Movie duration to integer';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE movie ADD duration_in_minutes INT NOT NULL');
        $this->addSql('ALTER TABLE movie DROP duration');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE movie ADD duration TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE movie DROP duration_in_minutes');
    }
}
