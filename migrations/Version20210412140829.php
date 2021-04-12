<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412140829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tricks (id INT AUTO_INCREMENT NOT NULL, group_trick_id INT NOT NULL, updated_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, video_urls LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_E1D902C1BBB1F251 (group_trick_id), INDEX IDX_E1D902C1896DBBDE (updated_by_id), INDEX IDX_E1D902C1B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1BBB1F251 FOREIGN KEY (group_trick_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tricks');
    }
}
