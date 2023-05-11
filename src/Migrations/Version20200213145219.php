<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200213145219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agency_country (agency_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_25735037CDEADB2A (agency_id), INDEX IDX_25735037F92F3E70 (country_id), PRIMARY KEY(agency_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign_country (campaign_id INT NOT NULL, country_id INT NOT NULL, INDEX IDX_FBC9554AF639F774 (campaign_id), INDEX IDX_FBC9554AF92F3E70 (country_id), PRIMARY KEY(campaign_id, country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agency_country ADD CONSTRAINT FK_25735037CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agency_country ADD CONSTRAINT FK_25735037F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE campaign_country ADD CONSTRAINT FK_FBC9554AF639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE campaign_country ADD CONSTRAINT FK_FBC9554AF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD last_login DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966CDEADB2A');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966F639F774');
        $this->addSql('DROP INDEX IDX_5373C966CDEADB2A ON country');
        $this->addSql('DROP INDEX IDX_5373C966F639F774 ON country');
        $this->addSql('ALTER TABLE country DROP agency_id, DROP campaign_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE agency_country');
        $this->addSql('DROP TABLE campaign_country');
        $this->addSql('ALTER TABLE country ADD agency_id INT DEFAULT NULL, ADD campaign_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5373C966CDEADB2A ON country (agency_id)');
        $this->addSql('CREATE INDEX IDX_5373C966F639F774 ON country (campaign_id)');
        $this->addSql('ALTER TABLE user DROP last_login');
    }
}
