<?php

declare(strict_types=1);

namespace EryseClient\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190805203847 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE user_roles (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54FCD59F5E237E06 ON user_roles (name)');
        $this->addSql('CREATE TABLE user_settings (id SERIAL NOT NULL, "user" INT NOT NULL, two_step_auth_enabled BOOLEAN NOT NULL, g_auth_secret VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE profiles (id SERIAL NOT NULL, "user" INT NOT NULL, state VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, birthdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE token_rememberMe (series CHAR(88) NOT NULL, value CHAR(88) NOT NULL, lastused TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series))');
        $this->addSql('CREATE TABLE token_types (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tokens (id SERIAL NOT NULL, type_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, "user" INT NOT NULL, invalid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AA5A118EC54C8C93 ON tokens (type_id)');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EC54C8C93 FOREIGN KEY (type_id) REFERENCES token_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tokens DROP CONSTRAINT FK_AA5A118EC54C8C93');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE profiles');
        $this->addSql('DROP TABLE token_rememberMe');
        $this->addSql('DROP TABLE token_types');
        $this->addSql('DROP TABLE tokens');
    }
}
