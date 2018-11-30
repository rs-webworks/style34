<?php declare(strict_types=1);

namespace Style34\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181128145822 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE profile DROP CONSTRAINT fk_8157aa0f5d83cc1');
        $this->addSql('DROP SEQUENCE state_id_seq CASCADE');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP INDEX idx_8157aa0f5d83cc1');
        $this->addSql('ALTER TABLE profile ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile DROP state_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE state_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE state (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE profile ADD state_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile DROP state');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT fk_8157aa0f5d83cc1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8157aa0f5d83cc1 ON profile (state_id)');
    }
}
