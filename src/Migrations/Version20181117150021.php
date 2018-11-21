<?php declare(strict_types=1);

namespace Style34\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181117150021 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE state (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE profile ADD state_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD birthdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8157AA0F5D83CC1 ON profile (state_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE profile DROP CONSTRAINT FK_8157AA0F5D83CC1');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP INDEX IDX_8157AA0F5D83CC1');
        $this->addSql('ALTER TABLE profile DROP state_id');
        $this->addSql('ALTER TABLE profile DROP activated_at');
        $this->addSql('ALTER TABLE profile DROP city');
        $this->addSql('ALTER TABLE profile DROP birthdate');
    }
}
