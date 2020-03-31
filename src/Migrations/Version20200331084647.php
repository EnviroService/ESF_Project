<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200331084647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rate_card DROP FOREIGN KEY FK_E9102ADCFEC09103');
        $this->addSql('DROP INDEX IDX_E9102ADCFEC09103 ON rate_card');
        $this->addSql('ALTER TABLE rate_card DROP simulation_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rate_card ADD simulation_id INT NOT NULL');
        $this->addSql('ALTER TABLE rate_card ADD CONSTRAINT FK_E9102ADCFEC09103 FOREIGN KEY (simulation_id) REFERENCES simulation (id)');
        $this->addSql('CREATE INDEX IDX_E9102ADCFEC09103 ON rate_card (simulation_id)');
    }
}
