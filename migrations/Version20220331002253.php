<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331002253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE people_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE people_address_last_view_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE people_phone_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE people_photo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE people (id INT NOT NULL, first_name VARCHAR(255) NOT NULL, second_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, birthday_date DATE DEFAULT NULL, address_residental VARCHAR(255) DEFAULT NULL, address_last VARCHAR(255) DEFAULT NULL, address_last_date DATE DEFAULT NULL, contacts VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN people.birthday_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN people.address_last_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE people_address_last_view (id INT NOT NULL, people_id INT NOT NULL, locality_value VARCHAR(255) NOT NULL, locality_type SMALLINT NOT NULL, address VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, date_start DATE DEFAULT NULL, date_end DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE3BCCFB3147C936 ON people_address_last_view (people_id)');
        $this->addSql('COMMENT ON COLUMN people_address_last_view.date_start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN people_address_last_view.date_end IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE people_phone (id INT NOT NULL, people_id INT NOT NULL, value VARCHAR(255) NOT NULL, note VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F2CED9D3147C936 ON people_phone (people_id)');
        $this->addSql('CREATE TABLE people_photo (id INT NOT NULL, people_id INT NOT NULL, filename VARCHAR(255) NOT NULL, priority SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFD4FE583147C936 ON people_photo (people_id)');
        $this->addSql('ALTER TABLE people_address_last_view ADD CONSTRAINT FK_DE3BCCFB3147C936 FOREIGN KEY (people_id) REFERENCES people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE people_phone ADD CONSTRAINT FK_8F2CED9D3147C936 FOREIGN KEY (people_id) REFERENCES people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE people_photo ADD CONSTRAINT FK_DFD4FE583147C936 FOREIGN KEY (people_id) REFERENCES people (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE people_address_last_view DROP CONSTRAINT FK_DE3BCCFB3147C936');
        $this->addSql('ALTER TABLE people_phone DROP CONSTRAINT FK_8F2CED9D3147C936');
        $this->addSql('ALTER TABLE people_photo DROP CONSTRAINT FK_DFD4FE583147C936');
        $this->addSql('DROP SEQUENCE people_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE people_address_last_view_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE people_phone_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE people_photo_id_seq CASCADE');
        $this->addSql('DROP TABLE people');
        $this->addSql('DROP TABLE people_address_last_view');
        $this->addSql('DROP TABLE people_phone');
        $this->addSql('DROP TABLE people_photo');
    }
}
