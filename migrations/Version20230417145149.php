<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230417145149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add halls table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE hall_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE hall (id INT NOT NULL, name VARCHAR(255) NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE hall_id_seq CASCADE');
        $this->addSql('DROP TABLE hall');
    }
}
