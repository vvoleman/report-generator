# Project Summary

**Toggl Track Report Generator** - A Symfony application for generating styled XLSX reports from Toggl Track time entries.

## Quick Overview

- **Framework**: Symfony 6.4 (PHP 8.2+)
- **Database**: PostgreSQL 16
- **Report Format**: XLSX (Excel)
- **Authentication**: OAuth2 (Toggl Track)
- **Template Engine**: Twig
- **Container**: Docker
- **License**: MIT

## What It Does

1. **User Management**: Register/login with email and password
2. **OAuth Integration**: Connect Toggl Track account securely
3. **Data Fetching**: Retrieve time entries for selected months
4. **Data Aggregation**: Group entries by project and day
5. **Report Generation**: Create styled Excel reports from templates
6. **Download**: Download XLSX reports with styling and formatting

## Key Features

### 🔐 Security
- Password hashing (bcrypt/argon2)
- OAuth2 token storage
- CSRF protection
- Role-based access control

### 📊 Reporting
- Multiple templates (Default, Detailed, Summary)
- Customizable via Twig
- Automatic Excel styling
- Project and time aggregation

### 🎨 User Experience
- Clean, modern UI
- Responsive design
- Flash messages for feedback
- Intuitive navigation

### 🐳 Deployment
- Complete Docker setup
- One-command startup
- Database migrations
- Environment configuration

## Technology Stack

### Backend
- **Symfony 6.4**: PHP framework
- **Doctrine ORM**: Database abstraction
- **KnpU OAuth2 Bundle**: OAuth2 client
- **PhpSpreadsheet**: XLSX generation
- **Twig**: Template engine

### Frontend
- **HTML5/CSS3**: Modern web standards
- **Minimal JavaScript**: No complex frameworks
- **Responsive Design**: Works on all devices

### Infrastructure
- **PHP 8.2-FPM**: Application runtime
- **PostgreSQL 16**: Database
- **Nginx**: Web server
- **Docker**: Containerization

## File Structure

```
├── config/              Configuration files
├── docker/              Docker setup
├── migrations/          Database migrations
├── public/              Web root
├── src/
│   ├── Controller/     HTTP controllers
│   ├── Entity/         Database entities
│   ├── OAuth/          OAuth providers
│   ├── Repository/     Data repositories
│   └── Service/        Business logic
├── templates/           Twig templates
├── var/                 Cache and logs
└── vendor/              Dependencies
```

## Main Components

### Entities
- **User**: Application user
- **TogglToken**: OAuth tokens

### Controllers
- **SecurityController**: Login/register
- **DashboardController**: Main page
- **OAuthController**: OAuth flow
- **ReportController**: Report generation

### Services
- **TogglApiService**: API communication
- **ReportAggregationService**: Data processing
- **ReportGeneratorService**: XLSX creation

### Templates
- Base layout with navigation
- Login/register forms
- Dashboard with connection status
- Report generation interface
- XLSX report templates (3 types)

## API Integration

**Toggl Track API v9**
- Endpoint: https://api.track.toggl.com/api/v9
- Authentication: OAuth2 Bearer token
- Used endpoints:
  - `/me` - User info
  - `/me/workspaces` - Workspaces
  - `/me/time_entries` - Time entries
  - `/workspaces/{id}/projects` - Projects

## Database Schema

### users
- Primary key: `id`
- Unique: `email`
- Hashed: `password`
- JSON: `roles`

### toggl_tokens
- Primary key: `id`
- Foreign key: `user_id`
- Encrypted: `access_token`, `refresh_token`
- Timestamps: `expires_at`, `created_at`

## Report Templates

Templates are Twig files with HTML tables that get converted to XLSX:

1. **default.html.twig**: Basic project and day grouping
2. **detailed.html.twig**: Individual entries with descriptions
3. **summary.html.twig**: Project totals only

Custom templates can be added by creating new Twig files in `templates/reports/`.

## Configuration Files

- `.env`: Default environment variables
- `.env.local`: Local overrides (not committed)
- `docker-compose.yml`: Docker services
- `config/packages/*.yaml`: Bundle configurations
- `config/routes.yaml`: Route definitions

## Available Commands

### Symfony Console
```bash
php bin/console about                    # System info
php bin/console debug:router             # List routes
php bin/console cache:clear              # Clear cache
php bin/console doctrine:migrations:migrate  # Run migrations
```

### Docker
```bash
docker-compose up -d                     # Start services
docker-compose down                      # Stop services
docker-compose logs -f                   # View logs
docker-compose exec php bash             # Access PHP container
```

### Composer
```bash
composer install                         # Install dependencies
composer update                          # Update dependencies
composer require package/name            # Add package
```

## Development Workflow

1. Clone repository
2. Copy `.env` to `.env.local`
3. Configure Toggl OAuth credentials
4. Run `./start.sh` or `docker-compose up -d`
5. Access http://localhost:8080
6. Register account
7. Connect Toggl
8. Generate reports

## Production Deployment

1. Set `APP_ENV=prod`
2. Configure production database
3. Set secure `APP_SECRET`
4. Update OAuth redirect URI
5. Enable HTTPS
6. Run migrations
7. Clear cache
8. Set proper file permissions

## Documentation

- **README.md**: Installation and usage
- **ARCHITECTURE.md**: Detailed architecture
- **OAUTH_SETUP.md**: OAuth configuration
- **TROUBLESHOOTING.md**: Common issues
- **SAMPLE_DATA.md**: Test data examples

## Testing

Currently manual testing recommended:

1. User registration/login
2. OAuth connection flow
3. Time entry fetching
4. Report generation
5. XLSX download and styling

Future: Add PHPUnit tests for services and controllers.

## Security Considerations

### Current
- Password hashing
- CSRF protection
- OAuth2 flow
- Database tokens

### Recommendations for Production
- Encrypt tokens at rest
- Implement token refresh
- Add rate limiting
- Enable HTTPS only
- Regular security audits
- Use secrets management

## Performance

### Current
- Suitable for individual users
- Handles typical monthly reports (<1000 entries)
- PHP-FPM with OPcache recommended

### Optimization Options
- Database query optimization
- Response caching
- Asset minification
- CDN for static files
- Read replicas for database

## Extensibility

Easy to extend:

1. **New report templates**: Add Twig file
2. **Additional OAuth providers**: Create provider class
3. **New aggregation types**: Extend aggregation service
4. **Custom styling**: Modify template CSS or Excel styles
5. **API endpoints**: Add new controllers

## Known Limitations

1. Token refresh not implemented (requires reconnection)
2. No batch report generation
3. Single user per Toggl connection
4. Limited error recovery in report generation
5. No report scheduling/automation

## Future Enhancements

Potential features:
- Token auto-refresh
- Multiple report formats (PDF, CSV)
- Report templates CRUD interface
- Scheduled report generation
- Email report delivery
- Multi-workspace support
- Report history and caching
- API endpoints for integrations

## Support and Contribution

- Report issues on GitHub
- Submit pull requests
- Follow coding standards
- Add tests for new features
- Update documentation

## Version Information

- Initial release: v1.0.0
- Symfony: 6.4.33
- PHP: 8.2+
- PostgreSQL: 16
- PhpSpreadsheet: 5.4.0

## Credits

Built with:
- Symfony Framework
- Doctrine ORM
- PhpSpreadsheet
- KnpU OAuth2 Client Bundle
- PostgreSQL
- Docker

## License

MIT License - See LICENSE file for details.
