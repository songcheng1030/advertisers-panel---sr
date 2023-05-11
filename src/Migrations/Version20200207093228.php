<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200207093228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE advertiser (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign (id INT AUTO_INCREMENT NOT NULL, agency_id INT DEFAULT NULL, advertiser_id INT DEFAULT NULL, ssp_id INT DEFAULT NULL, dsp_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type INT DEFAULT NULL, deal_id INT DEFAULT NULL, vtr INT DEFAULT NULL, viewability INT DEFAULT NULL, ctr INT DEFAULT NULL, volume INT DEFAULT NULL, list_type INT DEFAULT NULL, list LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', details LONGTEXT DEFAULT NULL, cpm DOUBLE PRECISION DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, rebate INT DEFAULT NULL, status SMALLINT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_1F1512DDCDEADB2A (agency_id), INDEX IDX_1F1512DDBA2FCBC2 (advertiser_id), INDEX IDX_1F1512DD7039F86D (ssp_id), INDEX IDX_1F1512DD6EEACA4E (dsp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, agency_id INT DEFAULT NULL, campaign_id INT DEFAULT NULL, iso VARCHAR(2) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, nice_name VARCHAR(255) DEFAULT NULL, iso3 VARCHAR(3) DEFAULT NULL, num_code SMALLINT DEFAULT NULL, phone_code INT DEFAULT NULL, INDEX IDX_5373C966CDEADB2A (agency_id), INDEX IDX_5373C966F639F774 (campaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ssp (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, sales_manager_head_id INT DEFAULT NULL, user_id INT DEFAULT NULL, country_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, status INT DEFAULT NULL, locale VARCHAR(5) DEFAULT NULL, picture LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, ip_address VARCHAR(15) DEFAULT NULL, nick VARCHAR(255) DEFAULT NULL, monthly_target DOUBLE PRECISION DEFAULT NULL, show_global_stats TINYINT(1) DEFAULT NULL, phone VARCHAR(100) DEFAULT NULL, comments LONGTEXT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649B03A8386 (created_by_id), INDEX IDX_8D93D649896DBBDE (updated_by_id), INDEX IDX_8D93D6493C59B8DC (sales_manager_head_id), INDEX IDX_8D93D649A76ED395 (user_id), INDEX IDX_8D93D649F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agency (id INT AUTO_INCREMENT NOT NULL, sales_manager_id INT DEFAULT NULL, billing_country_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type INT DEFAULT NULL, rebate INT DEFAULT NULL, billing_fiscal_status INT DEFAULT NULL, billing_nif_cif VARCHAR(255) DEFAULT NULL, billing_company VARCHAR(255) DEFAULT NULL, billing_city VARCHAR(255) DEFAULT NULL, billing_province VARCHAR(255) DEFAULT NULL, billing_cp VARCHAR(255) DEFAULT NULL, billing_address VARCHAR(255) DEFAULT NULL, details LONGTEXT DEFAULT NULL, account_manager JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_70C0C6E67702830F (sales_manager_id), INDEX IDX_70C0C6E6754851E1 (billing_country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dsp (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDCDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDBA2FCBC2 FOREIGN KEY (advertiser_id) REFERENCES advertiser (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD7039F86D FOREIGN KEY (ssp_id) REFERENCES ssp (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD6EEACA4E FOREIGN KEY (dsp_id) REFERENCES dsp (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493C59B8DC FOREIGN KEY (sales_manager_head_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE agency ADD CONSTRAINT FK_70C0C6E67702830F FOREIGN KEY (sales_manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE agency ADD CONSTRAINT FK_70C0C6E6754851E1 FOREIGN KEY (billing_country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DDBA2FCBC2');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966F639F774');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE agency DROP FOREIGN KEY FK_70C0C6E6754851E1');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD7039F86D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493C59B8DC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A76ED395');
        $this->addSql('ALTER TABLE agency DROP FOREIGN KEY FK_70C0C6E67702830F');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DDCDEADB2A');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966CDEADB2A');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DD6EEACA4E');
        $this->addSql('DROP TABLE advertiser');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE ssp');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE agency');
        $this->addSql('DROP TABLE dsp');
    }
}
