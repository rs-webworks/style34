<?php

declare(strict_types=1);

namespace EryseClient\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190801125913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');
        $this->addSql('CREATE TABLE settings (id SERIAL NOT NULL, "user" INT NOT NULL, two_step_auth_enabled BOOLEAN NOT NULL, g_auth_secret VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE profile (id SERIAL NOT NULL, "user" INT NOT NULL, state VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, birthdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE rememberme_token (series CHAR(88) NOT NULL, value CHAR(88) NOT NULL, lastused TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series))');
        $this->addSql('CREATE TABLE token_type (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE token (id SERIAL NOT NULL, type_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, "user" INT NOT NULL, invalid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F37A13BC54C8C93 ON token (type_id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BC54C8C93 FOREIGN KEY (type_id) REFERENCES token_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13BC54C8C93');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE rememberme_token');
        $this->addSql('DROP TABLE token_type');
        $this->addSql('DROP TABLE token');
    }
}
