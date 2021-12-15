<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215091902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift ADD buy_by_id INT DEFAULT NULL, DROP already_buy');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D98C1DABB FOREIGN KEY (buy_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_A47C990D98C1DABB ON gift (buy_by_id)');
        $this->addSql('ALTER TABLE pot ADD CONSTRAINT FK_1EBD730FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE pot ADD CONSTRAINT FK_1EBD730F97A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D98C1DABB');
        $this->addSql('DROP INDEX IDX_A47C990D98C1DABB ON gift');
        $this->addSql('ALTER TABLE gift ADD already_buy TINYINT(1) NOT NULL, DROP buy_by_id');
        $this->addSql('ALTER TABLE pot DROP FOREIGN KEY FK_1EBD730FA76ED395');
        $this->addSql('ALTER TABLE pot DROP FOREIGN KEY FK_1EBD730F97A95A83');
    }
}
