<?php

declare(strict_types=1);

namespace Trailmind\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030142809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP salt');
        $this->addSql('ALTER INDEX uniq_1483a5e9e7927c74 RENAME TO UNIQ_USER_EMAIL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER INDEX uniq_user_email RENAME TO uniq_1483a5e9e7927c74');
    }
}
