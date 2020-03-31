<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330145230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE options_simulation (options_id INT NOT NULL, simulation_id INT NOT NULL, INDEX IDX_939E79CE3ADB05F1 (options_id), INDEX IDX_939E79CEFEC09103 (simulation_id), PRIMARY KEY(options_id, simulation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE options_simulation ADD CONSTRAINT FK_939E79CE3ADB05F1 FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE options_simulation ADD CONSTRAINT FK_939E79CEFEC09103 FOREIGN KEY (simulation_id) REFERENCES simulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE simulation ADD devis_id INT NOT NULL');
        $this->addSql('ALTER TABLE simulation ADD CONSTRAINT FK_CBDA467B41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('CREATE INDEX IDX_CBDA467B41DEFADA ON simulation (devis_id)');
        $this->addSql('ALTER TABLE rate_card ADD simulation_id INT NOT NULL');
        $this->addSql('ALTER TABLE rate_card ADD CONSTRAINT FK_E9102ADCFEC09103 FOREIGN KEY (simulation_id) REFERENCES simulation (id)');
        $this->addSql('CREATE INDEX IDX_E9102ADCFEC09103 ON rate_card (simulation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE options_simulation');
        $this->addSql('ALTER TABLE rate_card DROP FOREIGN KEY FK_E9102ADCFEC09103');
        $this->addSql('DROP INDEX IDX_E9102ADCFEC09103 ON rate_card');
        $this->addSql('ALTER TABLE rate_card DROP simulation_id');
        $this->addSql('ALTER TABLE simulation DROP FOREIGN KEY FK_CBDA467B41DEFADA');
        $this->addSql('DROP INDEX IDX_CBDA467B41DEFADA ON simulation');
        $this->addSql('ALTER TABLE simulation DROP devis_id');
    }
}
