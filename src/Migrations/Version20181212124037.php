<?php declare(strict_types=1);

namespace Style34\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181212124037 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE profile DROP CONSTRAINT fk_8157aa0fd60322ac');
        $this->addSql('DROP INDEX idx_8157aa0fd60322ac');
        $this->addSql('ALTER TABLE profile ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE profile DROP role_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE profile ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile DROP roles');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT fk_8157aa0fd60322ac FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8157aa0fd60322ac ON profile (role_id)');
    }
}
