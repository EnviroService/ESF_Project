<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200401121914 extends AbstractMigration
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
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL, CHANGE ref_sign ref_sign VARCHAR(255) NOT NULL, CHANGE num_tva num_tva DOUBLE PRECISION NOT NULL, CHANGE billing_address billing_address VARCHAR(255) NOT NULL, CHANGE billing_city billing_city VARCHAR(255) NOT NULL, CHANGE billing_postcode billing_postcode VARCHAR(255) NOT NULL, CHANGE operational_address operational_address VARCHAR(255) NOT NULL, CHANGE operational_city operational_city VARCHAR(255) NOT NULL, CHANGE operational_postcode operational_postcode VARCHAR(255) NOT NULL, CHANGE ref_contact ref_contact VARCHAR(255) NOT NULL, CHANGE boss_name boss_name VARCHAR(255) NOT NULL');
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
        $this->addSql('ALTER TABLE simulation DROP FOREIGN KEY FK_CBDA467B11084CC8');
        $this->addSql('DROP INDEX IDX_CBDA467B11084CC8 ON simulation');
        $this->addSql('ALTER TABLE simulation DROP ratecard_id');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE ref_sign ref_sign VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE num_tva num_tva VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE billing_address billing_address VARCHAR(55) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE billing_city billing_city VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE billing_postcode billing_postcode VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE operational_address operational_address VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE operational_city operational_city VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE operational_postcode operational_postcode VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE ref_contact ref_contact VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE boss_name boss_name VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
