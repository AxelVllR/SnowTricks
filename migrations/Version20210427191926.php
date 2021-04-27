<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210427191926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C11CDA489C');
        $this->addSql('DROP INDEX UNIQ_E1D902C11CDA489C ON tricks');
        $this->addSql('ALTER TABLE tricks DROP primary_image_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks ADD primary_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C11CDA489C FOREIGN KEY (primary_image_id) REFERENCES tricks_pictures (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D902C11CDA489C ON tricks (primary_image_id)');
    }
}
