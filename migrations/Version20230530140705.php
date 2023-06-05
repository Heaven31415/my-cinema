<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230530140705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add shows table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE show_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE show (id INT NOT NULL, movie_id UUID NOT NULL, hall_id INT NOT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_320ED9018F93B6FC ON show (movie_id)');
        $this->addSql('CREATE INDEX IDX_320ED90152AFCFD6 ON show (hall_id)');
        $this->addSql('COMMENT ON COLUMN show.movie_id IS \'(DC2Type:uuid)\'');
        $this->addSql(
            'ALTER TABLE show ADD CONSTRAINT FK_320ED9018F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE show ADD CONSTRAINT FK_320ED90152AFCFD6 FOREIGN KEY (hall_id) REFERENCES hall (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE show_id_seq CASCADE');
        $this->addSql('ALTER TABLE show DROP CONSTRAINT FK_320ED9018F93B6FC');
        $this->addSql('ALTER TABLE show DROP CONSTRAINT FK_320ED90152AFCFD6');
        $this->addSql('DROP TABLE show');
    }
}
