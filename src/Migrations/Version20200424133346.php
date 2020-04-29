<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200424133346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_booking DATETIME NOT NULL, is_received TINYINT(1) NOT NULL, received_date DATETIME DEFAULT NULL, INDEX IDX_E00CEDDEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tracking (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, imei VARCHAR(255) NOT NULL, is_sent TINYINT(1) NOT NULL, sent_date DATETIME DEFAULT NULL, is_received TINYINT(1) NOT NULL, received_date DATETIME DEFAULT NULL, is_repaired TINYINT(1) NOT NULL, repaired_date DATETIME DEFAULT NULL, is_returned TINYINT(1) NOT NULL, returned_date DATETIME DEFAULT NULL, INDEX IDX_A87C621C3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, tracking_id INT NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_5A8600B07D05ABBE (tracking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution (id INT AUTO_INCREMENT NOT NULL, tracking_id INT NOT NULL, solution VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, prestation VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_9F3329DB7D05ABBE (tracking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tracking ADD CONSTRAINT FK_A87C621C3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B07D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DB7D05ABBE FOREIGN KEY (tracking_id) REFERENCES tracking (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tracking DROP FOREIGN KEY FK_A87C621C3301C60');
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B07D05ABBE');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DB7D05ABBE');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE tracking');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE solution');
    }
}
