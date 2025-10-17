<?php

declare(strict_types=1);

namespace Trailmind\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create users table with UUID primary key and email unique constraint
 */
final class Version20251017105900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table with UUID primary key and email unique constraint';
    }

    public function up(Schema $schema): void
    {
        // Create users table for authentication with proper constraints
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, salt VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USER_EMAIL ON users (email)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // Remove users table
        $this->addSql('DROP TABLE users');
    }
}