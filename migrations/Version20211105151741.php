<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211105151741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gift_gift_group (gift_id INT NOT NULL, gift_group_id INT NOT NULL, INDEX IDX_D519D39597A95A83 (gift_id), INDEX IDX_D519D395B8513D0D (gift_group_id), PRIMARY KEY(gift_id, gift_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_group (id INT AUTO_INCREMENT NOT NULL, ask_by_id INT NOT NULL, child_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, expire_date DATETIME NOT NULL, INDEX IDX_78E8147E9762621E (ask_by_id), INDEX IDX_78E8147EDD62C21B (child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_gift_group ADD CONSTRAINT FK_D519D39597A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gift_gift_group ADD CONSTRAINT FK_D519D395B8513D0D FOREIGN KEY (gift_group_id) REFERENCES gift_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gift_group ADD CONSTRAINT FK_78E8147E9762621E FOREIGN KEY (ask_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE gift_group ADD CONSTRAINT FK_78E8147EDD62C21B FOREIGN KEY (child_id) REFERENCES child (id)');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D9762621E');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DDD62C21B');
        $this->addSql('DROP INDEX IDX_A47C990D9762621E ON gift');
        $this->addSql('DROP INDEX IDX_A47C990DDD62C21B ON gift');
        $this->addSql('ALTER TABLE gift DROP ask_by_id, DROP child_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift_gift_group DROP FOREIGN KEY FK_D519D395B8513D0D');
        $this->addSql('DROP TABLE gift_gift_group');
        $this->addSql('DROP TABLE gift_group');
        $this->addSql('ALTER TABLE gift ADD ask_by_id INT NOT NULL, ADD child_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D9762621E FOREIGN KEY (ask_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DDD62C21B FOREIGN KEY (child_id) REFERENCES child (id)');
        $this->addSql('CREATE INDEX IDX_A47C990D9762621E ON gift (ask_by_id)');
        $this->addSql('CREATE INDEX IDX_A47C990DDD62C21B ON gift (child_id)');
    }
}
