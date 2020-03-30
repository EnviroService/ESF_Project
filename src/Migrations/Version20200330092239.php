<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330092239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE simulation (id INT AUTO_INCREMENT NOT NULL, factures_id INT DEFAULT NULL, quantity VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CBDA467BE9D518F9 (factures_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE options (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, price_option INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE options_simulation (options_id INT NOT NULL, simulation_id INT NOT NULL, INDEX IDX_939E79CE3ADB05F1 (options_id), INDEX IDX_939E79CEFEC09103 (simulation_id), PRIMARY KEY(options_id, simulation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factures (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_647590BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rate_card (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, solution VARCHAR(255) NOT NULL, prestation VARCHAR(255) NOT NULL, models VARCHAR(255) NOT NULL, price_rate_card INT NOT NULL, UNIQUE INDEX UNIQ_E9102ADC7F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_8B27C52BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE simulation ADD CONSTRAINT FK_CBDA467BE9D518F9 FOREIGN KEY (factures_id) REFERENCES factures (id)');
        $this->addSql('ALTER TABLE options_simulation ADD CONSTRAINT FK_939E79CE3ADB05F1 FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE options_simulation ADD CONSTRAINT FK_939E79CEFEC09103 FOREIGN KEY (simulation_id) REFERENCES simulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE factures ADD CONSTRAINT FK_647590BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rate_card ADD CONSTRAINT FK_E9102ADC7F2DEE08 FOREIGN KEY (facture_id) REFERENCES factures (id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD bonus_rate_card DOUBLE PRECISION NOT NULL, ADD bonus_option DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE options_simulation DROP FOREIGN KEY FK_939E79CEFEC09103');
        $this->addSql('ALTER TABLE options_simulation DROP FOREIGN KEY FK_939E79CE3ADB05F1');
        $this->addSql('ALTER TABLE simulation DROP FOREIGN KEY FK_CBDA467BE9D518F9');
        $this->addSql('ALTER TABLE rate_card DROP FOREIGN KEY FK_E9102ADC7F2DEE08');
        $this->addSql('DROP TABLE simulation');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE options_simulation');
        $this->addSql('DROP TABLE factures');
        $this->addSql('DROP TABLE rate_card');
        $this->addSql('DROP TABLE devis');
        $this->addSql('ALTER TABLE user DROP bonus_rate_card, DROP bonus_option');
    }
}
