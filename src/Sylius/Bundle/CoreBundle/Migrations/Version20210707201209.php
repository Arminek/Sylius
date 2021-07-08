<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707201209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE process_job');
        $this->addSql('ALTER TABLE Job ADD process_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Job ADD CONSTRAINT FK_C395A6187EC2F574 FOREIGN KEY (process_id) REFERENCES Process (id)');
        $this->addSql('CREATE INDEX IDX_C395A6187EC2F574 ON Job (process_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE process_job (process_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_C71AFFBB7EC2F574 (process_id), INDEX IDX_C71AFFBBBE04EA9 (job_id), PRIMARY KEY(process_id, job_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE process_job ADD CONSTRAINT FK_C71AFFBB7EC2F574 FOREIGN KEY (process_id) REFERENCES Process (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_job ADD CONSTRAINT FK_C71AFFBBBE04EA9 FOREIGN KEY (job_id) REFERENCES Job (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Job DROP FOREIGN KEY FK_C395A6187EC2F574');
        $this->addSql('DROP INDEX IDX_C395A6187EC2F574 ON Job');
        $this->addSql('ALTER TABLE Job DROP process_id');
    }
}
