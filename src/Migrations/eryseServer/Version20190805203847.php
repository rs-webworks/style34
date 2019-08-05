<?php

declare(strict_types=1);

namespace EryseServer\Migrations;

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

        $this->addSql('CREATE TABLE user_devices (id SERIAL NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, cookie_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490A50905E237E06 ON user_devices (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490A5090F35826F ON user_devices (cookie_name)');
        $this->addSql('CREATE INDEX IDX_490A5090A76ED395 ON user_devices (user_id)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles JSON NOT NULL, last_ip VARCHAR(255) NOT NULL, registered_as VARCHAR(255) NOT NULL, settings_id INT NOT NULL, trusted_token_version INT NOT NULL, profile_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE user_devices ADD CONSTRAINT FK_490A5090A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_devices DROP CONSTRAINT FK_490A5090A76ED395');
        $this->addSql('DROP TABLE user_devices');
        $this->addSql('DROP TABLE users');
    }
}
