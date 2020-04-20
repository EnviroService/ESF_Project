<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415130848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enseignes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, adress VARCHAR(255) NOT NULL, postcode VARCHAR(10) NOT NULL, city VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD enseigne_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseignes (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496C2A0A71 ON user (enseigne_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496C2A0A71');
        $this->addSql('DROP TABLE enseignes');
        $this->addSql('DROP INDEX IDX_8D93D6496C2A0A71 ON user');
        $this->addSql('ALTER TABLE user DROP enseigne_id');
    }
}
