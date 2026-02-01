# Troubleshooting Guide

This guide helps you solve common issues when setting up or using the Toggl Report Generator.

## Installation Issues

### "composer: command not found"

**Solution**: Install Composer
```bash
# Download and install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### "Docker is not running"

**Solution**: Start Docker service
```bash
# On Linux
sudo systemctl start docker

# On macOS/Windows
# Start Docker Desktop application
```

### "Port 8080 is already in use"

**Solution**: Change the port in docker-compose.yml
```yaml
services:
  nginx:
    ports:
      - "8081:80"  # Change 8080 to another port
```

### "Port 5432 is already in use"

**Solution**: Either stop your local PostgreSQL or change the database port
```yaml
services:
  database:
    ports:
      - "5433:5432"  # Change external port
```

Then update DATABASE_URL in .env.local accordingly.

## Database Issues

### "Connection refused" when running migrations

**Problem**: Database container is not ready yet

**Solution**: Wait a few seconds for PostgreSQL to start
```bash
# Check if database is running
docker-compose ps

# Check database logs
docker-compose logs database

# Wait and retry
sleep 5
docker-compose exec php php bin/console doctrine:migrations:migrate
```

### "Database does not exist"

**Solution**: Create the database
```bash
docker-compose exec php php bin/console doctrine:database:create
```

### "Table already exists" error

**Solution**: Reset the database
```bash
docker-compose exec php php bin/console doctrine:database:drop --force
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## OAuth Issues

### "Invalid client_id or client_secret"

**Causes**:
1. Wrong credentials in .env.local
2. Extra spaces in environment variables
3. Using test credentials in production

**Solution**:
```bash
# Check your .env.local file
cat .env.local | grep OAUTH_TOGGL

# Verify no quotes around values
OAUTH_TOGGL_CLIENT_ID=actual_id_here
OAUTH_TOGGL_CLIENT_SECRET=actual_secret_here

# Clear cache
docker-compose exec php php bin/console cache:clear
```

### "Redirect URI mismatch"

**Problem**: The redirect URI in your Toggl OAuth app doesn't match

**Solution**: Ensure redirect URI matches exactly:
- Development: `http://localhost:8080/oauth/check/toggl`
- Production: `https://yourdomain.com/oauth/check/toggl`

### "Could not authenticate against github.com"

**Problem**: Composer is trying to download packages from GitHub

**Solution**: This is usually not a critical error. Composer will fall back to cloning from source. If it persists:
```bash
# Generate GitHub token at https://github.com/settings/tokens
composer config -g github-oauth.github.com YOUR_GITHUB_TOKEN
```

## Application Issues

### "Template not found"

**Problem**: Trying to use a template that doesn't exist

**Solution**: Check available templates
```bash
ls templates/reports/
# Should show: default.html.twig, detailed.html.twig, summary.html.twig
```

### "No time entries found"

**Causes**:
1. No time entries in selected month
2. OAuth token expired
3. Toggl API error

**Solution**:
```bash
# Reconnect Toggl account
# Go to Dashboard -> Connect Toggl Account

# Check logs
docker-compose logs php

# Verify Toggl API is accessible
curl -H "Authorization: Bearer YOUR_TOKEN" https://api.track.toggl.com/api/v9/me
```

### "500 Internal Server Error"

**Solution**: Check application logs
```bash
# View logs
docker-compose logs php

# Check Symfony logs
docker-compose exec php tail -f var/log/dev.log

# Enable debug mode (if not already)
# In .env: APP_ENV=dev
```

### "Permission denied" errors

**Problem**: File permission issues in Docker

**Solution**: Fix permissions
```bash
# Fix permissions for var directory
docker-compose exec php chmod -R 777 var/

# Or run as the correct user
docker-compose exec -u www-data php php bin/console cache:clear
```

## Report Generation Issues

### "XLSX file is empty or corrupted"

**Causes**:
1. Template syntax error
2. Missing data
3. PhpSpreadsheet error

**Solution**:
```bash
# Check template syntax
docker-compose exec php php bin/console lint:twig templates/reports/

# View detailed error
docker-compose exec php tail -f var/log/dev.log

# Try default template first
```

### "Report download doesn't start"

**Solution**:
```bash
# Check browser console for errors
# Verify temp directory is writable
docker-compose exec php ls -la /tmp/
docker-compose exec php chmod -R 777 /tmp/
```

## Performance Issues

### "Slow report generation"

**Solution**:
```bash
# Enable OPcache in production
# Add to docker/php/Dockerfile:
RUN docker-php-ext-install opcache

# Increase PHP memory limit
# In docker/php/Dockerfile:
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini
```

### "Database queries are slow"

**Solution**: Add indexes
```php
// In your entity annotations
#[ORM\Index(name="idx_created_at", columns=["created_at"])]
```

## Docker Issues

### "Cannot remove container"

**Solution**: Force remove
```bash
docker-compose down -v
docker-compose up -d
```

### "Out of disk space"

**Solution**: Clean up Docker
```bash
# Remove unused images
docker image prune -a

# Remove unused volumes
docker volume prune

# Remove everything unused
docker system prune -a --volumes
```

### "Container keeps restarting"

**Solution**: Check logs
```bash
docker-compose logs php
docker-compose logs nginx
docker-compose logs database
```

## Production Issues

### "Assets not loading"

**Solution**: Install assets
```bash
php bin/console assets:install public --symlink
```

### "Slow performance in production"

**Solution**: Optimize for production
```bash
# Set environment to prod
APP_ENV=prod

# Install dependencies without dev
composer install --no-dev --optimize-autoloader

# Clear and warm up cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Security Issues

### "CSRF token invalid"

**Solution**: Clear sessions
```bash
# Clear session files
rm -rf var/sessions/*

# Or clear cache
php bin/console cache:clear
```

### "Token expired"

**Problem**: OAuth token has expired

**Solution**: Implement token refresh or ask user to reconnect
```bash
# User should go to Dashboard and click "Connect Toggl Account" again
```

## Getting Help

If you can't resolve your issue:

1. **Check logs**: Always start by checking logs
   ```bash
   docker-compose logs -f
   tail -f var/log/dev.log
   ```

2. **Enable debug mode**: In .env.local set `APP_ENV=dev`

3. **Check GitHub Issues**: Look for similar issues in the repository

4. **Create an issue**: Include:
   - Error message
   - Steps to reproduce
   - Your environment (OS, Docker version, PHP version)
   - Relevant logs

## Useful Commands

```bash
# Restart all services
docker-compose restart

# View all logs
docker-compose logs -f

# Access PHP container
docker-compose exec php bash

# Run Symfony commands
docker-compose exec php php bin/console [command]

# Check service status
docker-compose ps

# Clean restart
docker-compose down
docker-compose up -d

# View database
docker-compose exec database psql -U app -d app
```

## Common Command Reference

```bash
# Clear cache
php bin/console cache:clear

# List routes
php bin/console debug:router

# List services
php bin/console debug:container

# Create migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Check security vulnerabilities
composer audit

# Update dependencies
composer update
```
