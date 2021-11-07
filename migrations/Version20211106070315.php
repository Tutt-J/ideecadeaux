<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211106070315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift ADD user_id INT NOT NULL, ADD child_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DDD62C21B FOREIGN KEY (child_id) REFERENCES child (id)');
        $this->addSql('CREATE INDEX IDX_A47C990DA76ED395 ON gift (user_id)');
        $this->addSql('CREATE INDEX IDX_A47C990DDD62C21B ON gift (child_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DA76ED395');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DDD62C21B');
        $this->addSql('DROP INDEX IDX_A47C990DA76ED395 ON gift');
        $this->addSql('DROP INDEX IDX_A47C990DDD62C21B ON gift');
        $this->addSql('ALTER TABLE gift DROP user_id, DROP child_id');
    }
}
