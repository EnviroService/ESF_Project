<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200327123055 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD ref_sign VARCHAR(255) NOT NULL, ADD siret INT NOT NULL, ADD num_tva DOUBLE PRECISION NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD billing_address VARCHAR(255) NOT NULL, ADD billing_city VARCHAR(255) NOT NULL, ADD billing_postcode VARCHAR(255) NOT NULL, ADD justify_doc TINYINT(1) NOT NULL, ADD operational_address VARCHAR(255) NOT NULL, ADD operational_city VARCHAR(255) NOT NULL, ADD operational_postcode VARCHAR(255) NOT NULL, ADD ref_contact VARCHAR(255) NOT NULL, ADD boss_name VARCHAR(255) NOT NULL, ADD signin_date DATETIME NOT NULL, ADD signup_date DATETIME NOT NULL, ADD erp_client VARCHAR(255) NOT NULL, ADD kbis VARCHAR(255) NOT NULL, ADD cni VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP ref_sign, DROP siret, DROP num_tva, DROP email, DROP billing_address, DROP billing_city, DROP billing_postcode, DROP justify_doc, DROP operational_address, DROP operational_city, DROP operational_postcode, DROP ref_contact, DROP boss_name, DROP signin_date, DROP signup_date, DROP erp_client, DROP kbis, DROP cni');
    }
}
