<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250709173122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op: duplicate of Version20250709112238 (created_at/date on offering).';
    }

    public function up(Schema $schema): void
    {
        // Duplicate of Version20250709112238 — columns already exist.
    }

    public function down(Schema $schema): void
    {
        // No-op (see up).
    }
}
