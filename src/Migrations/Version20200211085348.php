<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200211085348 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reportsresume (id INT AUTO_INCREMENT NOT NULL, iduser INT NOT NULL, id_tag INT NOT NULL, domain INT NOT NULL, country INT NOT NULL, impressions INT NOT NULL, opportunities INT NOT NULL, revenue NUMERIC(20, 16) NOT NULL, coste NUMERIC(20, 16) NOT NULL, extra_prima_p NUMERIC(4, 2) NOT NULL, clicks INT NOT NULL, wins INT NOT NULL, ad_starts INT NOT NULL, first_quartiles INT NOT NULL, extraprima NUMERIC(20, 16) NOT NULL, mid_views INT NOT NULL, third_quartiles INT NOT NULL, completed_views INT NOT NULL, time_added INT NOT NULL, last_update INT NOT NULL, date DATE NOT NULL, idsite INT NOT NULL, formatloads INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_key (id INT(11) NOT NULL AUTO_INCREMENT, user_id INT(11) NOT NULL, unique_id VARCHAR(128) NOT NULL DEFAULT \'\' COLLATE `utf8mb4_unicode_ci`, type INT(11) NOT NULL, status INT(11) NULL DEFAULT \'0\', created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`), INDEX `user_id` (`user_id`)) COLLATE=`utf8mb4_unicode_ci` ENGINE=InnoDB');


        
        ;
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reportsresume');
        $this->addSql('DROP TABLE report_key');
    }
}
