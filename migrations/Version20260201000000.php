<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260201000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users and toggl_tokens tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id SERIAL PRIMARY KEY, email VARCHAR(180) NOT NULL UNIQUE, roles JSON NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE toggl_tokens (id SERIAL PRIMARY KEY, user_id INT NOT NULL, access_token TEXT NOT NULL, refresh_token TEXT, expires_at TIMESTAMP NOT NULL, created_at TIMESTAMP NOT NULL, CONSTRAINT fk_toggl_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)');
        $this->addSql('CREATE INDEX idx_toggl_tokens_user ON toggl_tokens(user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE toggl_tokens');
        $this->addSql('DROP TABLE users');
    }
}
