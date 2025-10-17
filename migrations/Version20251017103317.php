<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251017103317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create trails table with UUID primary key';
    }

    public function up(Schema $schema): void
    {
        // Create the trails table for storing trail information
        $this->addSql('CREATE TABLE trails (id UUID NOT NULL, name VARCHAR(255) NOT NULL, difficulty VARCHAR(255) NOT NULL, length DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN trails.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // Remove the trails table
        $this->addSql('DROP TABLE trails');
    }
}
