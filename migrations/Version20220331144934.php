<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331144934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE people DROP address_last');
        $this->addSql('ALTER TABLE people DROP address_last_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE people ADD address_last VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE people ADD address_last_date DATE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN people.address_last_date IS \'(DC2Type:date_immutable)\'');
    }
}
