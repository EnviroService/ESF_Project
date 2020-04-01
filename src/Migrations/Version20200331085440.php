<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200331085440 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE simulation ADD ratecard_id INT NOT NULL');
        $this->addSql('ALTER TABLE simulation ADD CONSTRAINT FK_CBDA467B11084CC8 FOREIGN KEY (ratecard_id) REFERENCES rate_card (id)');
        $this->addSql('CREATE INDEX IDX_CBDA467B11084CC8 ON simulation (ratecard_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE simulation DROP FOREIGN KEY FK_CBDA467B11084CC8');
        $this->addSql('DROP INDEX IDX_CBDA467B11084CC8 ON simulation');
        $this->addSql('ALTER TABLE simulation DROP ratecard_id');
    }
}
