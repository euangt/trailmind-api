<?php

declare(strict_types=1);

namespace Trailmind\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106111909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trail_points (id UUID NOT NULL, trail_id UUID NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, elevation DOUBLE PRECISION DEFAULT NULL, sequence_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F093CC4789B51C5B ON trail_points (trail_id)');
        $this->addSql('COMMENT ON COLUMN trail_points.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN trail_points.trail_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE trail_points ADD CONSTRAINT FK_F093CC4789B51C5B FOREIGN KEY (trail_id) REFERENCES trails (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trails ADD start_point_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE trails ADD end_point_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE trails ADD route geography(LINESTRING, 4326) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN trails.start_point_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN trails.end_point_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE trails ADD CONSTRAINT FK_66BB393FDF028890 FOREIGN KEY (start_point_id) REFERENCES trail_points (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trails ADD CONSTRAINT FK_66BB393F196B5B2F FOREIGN KEY (end_point_id) REFERENCES trail_points (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_66BB393FDF028890 ON trails (start_point_id)');
        $this->addSql('CREATE INDEX IDX_66BB393F196B5B2F ON trails (end_point_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trails DROP CONSTRAINT FK_66BB393FDF028890');
        $this->addSql('ALTER TABLE trails DROP CONSTRAINT FK_66BB393F196B5B2F');
        $this->addSql('ALTER TABLE trail_points DROP CONSTRAINT FK_F093CC4789B51C5B');
        $this->addSql('DROP TABLE trail_points');
        $this->addSql('DROP INDEX IDX_66BB393FDF028890');
        $this->addSql('DROP INDEX IDX_66BB393F196B5B2F');
        $this->addSql('ALTER TABLE trails DROP start_point_id');
        $this->addSql('ALTER TABLE trails DROP end_point_id');
        $this->addSql('ALTER TABLE trails DROP route');
    }
}
