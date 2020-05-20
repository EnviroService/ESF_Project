<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520120712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE simulation DROP FOREIGN KEY FK_CBDA467B7D05ABBE');
        $this->addSql('DROP INDEX UNIQ_CBDA467B7D05ABBE ON simulation');
        $this->addSql('ALTER TABLE simulation DROP tracking_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE simulation ADD tracking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE simulation ADD CONSTRAINT FK_CBDA467B7D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBDA467B7D05ABBE ON simulation (tracking_id)');
    }
}
