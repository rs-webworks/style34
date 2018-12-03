<?php declare(strict_types=1);

namespace Style34\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181203135946 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE token (id SERIAL NOT NULL, type_id INT DEFAULT NULL, profile_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F37A13BC54C8C93 ON token (type_id)');
        $this->addSql('CREATE INDEX IDX_5F37A13BCCFA12B8 ON token (profile_id)');
        $this->addSql('CREATE TABLE token_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BC54C8C93 FOREIGN KEY (type_id) REFERENCES token_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13BC54C8C93');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE token_type');
    }
}
