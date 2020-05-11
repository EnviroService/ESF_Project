<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200507140951 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE factures ADD booking_id INT NOT NULL, ADD date_edit DATETIME NOT NULL, ADD is_paid TINYINT(1) NOT NULL, ADD date_payment DATETIME DEFAULT NULL, ADD amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE factures ADD CONSTRAINT FK_647590B3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_647590B3301C60 ON factures (booking_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE factures DROP FOREIGN KEY FK_647590B3301C60');
        $this->addSql('DROP INDEX UNIQ_647590B3301C60 ON factures');
        $this->addSql('ALTER TABLE factures DROP booking_id, DROP date_edit, DROP is_paid, DROP date_payment, DROP amount');
    }
}
