<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704134010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE offering ADD fiangonana_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offering ADD CONSTRAINT FK_A5682AB15F9D98F6 FOREIGN KEY (fiangonana_id) REFERENCES fiangonana (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A5682AB15F9D98F6 ON offering (fiangonana_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB15F9D98F6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A5682AB15F9D98F6 ON offering
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offering DROP fiangonana_id
        SQL);
    }
}
