<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230427131257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename movies table column name from length to duration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE movie RENAME COLUMN length TO duration');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE movie RENAME COLUMN duration TO length');
    }
}
