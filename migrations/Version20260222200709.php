<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222200709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE domain_events (
            id VARCHAR(36) NOT NULL PRIMARY KEY,
            aggregate_id VARCHAR(64) NOT NULL,
            name VARCHAR(255) NOT NULL,
            payload LONGTEXT NOT NULL,
            occurred_on DATETIME NOT NULL,
            version INT NOT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE domain_events');
    }
}
