<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502133430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hobby CHANGE update_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job CHANGE update_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE personne CHANGE update_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE profile CHANGE update_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hobby CHANGE updated_at update_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE job CHANGE updated_at update_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE personne CHANGE updated_at update_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE profile CHANGE updated_at update_at DATETIME DEFAULT NULL');
    }
}