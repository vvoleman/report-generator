<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260201220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update toggl_tokens table to use API token instead of OAuth tokens';
    }

    public function up(Schema $schema): void
    {
        // Rename access_token to api_token, remove refresh_token and expires_at, add updated_at
        $this->addSql('ALTER TABLE toggl_tokens RENAME COLUMN access_token TO api_token');
        $this->addSql('ALTER TABLE toggl_tokens DROP COLUMN refresh_token');
        $this->addSql('ALTER TABLE toggl_tokens DROP COLUMN expires_at');
        $this->addSql('ALTER TABLE toggl_tokens ADD COLUMN updated_at TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // Reverse the changes
        $this->addSql('ALTER TABLE toggl_tokens RENAME COLUMN api_token TO access_token');
        $this->addSql('ALTER TABLE toggl_tokens ADD COLUMN refresh_token TEXT');
        $this->addSql('ALTER TABLE toggl_tokens ADD COLUMN expires_at TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE toggl_tokens DROP COLUMN updated_at');
    }
}
