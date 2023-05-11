<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210182226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE advertiser ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE campaign ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ssp ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE agency ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dsp ADD deleted TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE advertiser DROP deleted');
        $this->addSql('ALTER TABLE agency DROP deleted');
        $this->addSql('ALTER TABLE campaign DROP deleted');
        $this->addSql('ALTER TABLE dsp DROP deleted');
        $this->addSql('ALTER TABLE ssp DROP deleted');
    }
}
