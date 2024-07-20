<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720123511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forecast (id UUID NOT NULL, source VARCHAR(255) DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, weather VARCHAR(255) DEFAULT NULL, temp VARCHAR(255) DEFAULT NULL, wind VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX source_idx ON forecast (source)');
        $this->addSql('CREATE INDEX date_idx ON forecast (date)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE forecast');
    }
}
