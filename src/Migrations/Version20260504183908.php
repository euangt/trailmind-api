<?php

declare(strict_types=1);

namespace Trailmind\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504183908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hikes (id SERIAL NOT NULL, trail_id UUID NOT NULL, user_id UUID NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BC4AE8BD89B51C5B ON hikes (trail_id)');
        $this->addSql('CREATE INDEX IDX_BC4AE8BDA76ED395 ON hikes (user_id)');
        $this->addSql('COMMENT ON COLUMN hikes.trail_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN hikes.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE hikes ADD CONSTRAINT FK_BC4AE8BD89B51C5B FOREIGN KEY (trail_id) REFERENCES trails (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hikes ADD CONSTRAINT FK_BC4AE8BDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA topology');
        $this->addSql('CREATE SCHEMA trail_topology');
        $this->addSql('ALTER TABLE hikes DROP CONSTRAINT FK_BC4AE8BD89B51C5B');
        $this->addSql('ALTER TABLE hikes DROP CONSTRAINT FK_BC4AE8BDA76ED395');
        $this->addSql('DROP TABLE hikes');
    }
}
