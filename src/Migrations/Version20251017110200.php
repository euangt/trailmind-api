<?php

declare(strict_types=1);

namespace Trailmind\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Set up PostGIS topology extension for spatial/geographic data
 */
final class Version20251017110200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set up PostGIS topology extension for spatial/geographic trail data';
    }

    public function up(Schema $schema): void
    {
        // Enable PostGIS extension (if not already enabled)
        $this->addSql('CREATE EXTENSION IF NOT EXISTS postgis');

        // Enable topology extension for advanced spatial operations
        $this->addSql('CREATE EXTENSION IF NOT EXISTS postgis_topology');

        // Create topology schema and initialize topology system
        $this->addSql('SELECT topology.CreateTopology(\'trail_topology\', 4326, 0.0001)');
    }

    public function down(Schema $schema): void
    {
        // Drop the topology and clean up
        $this->addSql('SELECT topology.DropTopology(\'trail_topology\')');

        // Remove extensions (be careful - these might be used by other apps)
        $this->addSql('DROP EXTENSION IF EXISTS postgis_topology CASCADE');
        $this->addSql('DROP EXTENSION IF EXISTS postgis CASCADE');
    }
}