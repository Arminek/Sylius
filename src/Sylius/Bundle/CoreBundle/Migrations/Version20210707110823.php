<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707110823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, workerName VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Process (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, progress INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE process_job (process_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_C71AFFBB7EC2F574 (process_id), INDEX IDX_C71AFFBBBE04EA9 (job_id), PRIMARY KEY(process_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process_job ADD CONSTRAINT FK_C71AFFBB7EC2F574 FOREIGN KEY (process_id) REFERENCES Process (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_job ADD CONSTRAINT FK_C71AFFBBBE04EA9 FOREIGN KEY (job_id) REFERENCES Job (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE process_job DROP FOREIGN KEY FK_C71AFFBBBE04EA9');
        $this->addSql('ALTER TABLE process_job DROP FOREIGN KEY FK_C71AFFBB7EC2F574');
        $this->addSql('DROP TABLE Job');
        $this->addSql('DROP TABLE Process');
        $this->addSql('DROP TABLE process_job');
    }
}
