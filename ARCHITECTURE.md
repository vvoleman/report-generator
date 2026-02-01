# Application Structure

## Directory Layout

```
report-generator/
├── assets/                 # Frontend assets (JS, CSS)
├── bin/                    # Executable files (console)
├── config/                 # Configuration files
│   ├── packages/          # Bundle configurations
│   └── routes/            # Route configurations
├── docker/                 # Docker configuration files
│   ├── nginx/             # Nginx configuration
│   └── php/               # PHP Dockerfile
├── migrations/            # Database migrations
├── public/                # Web root
│   └── index.php         # Front controller
├── src/                   # Application source code
│   ├── Controller/       # HTTP controllers
│   ├── Entity/           # Doctrine entities
│   ├── OAuth/            # Custom OAuth providers
│   ├── Repository/       # Database repositories
│   └── Service/          # Business logic services
├── templates/             # Twig templates
│   ├── dashboard/        # Dashboard templates
│   ├── reports/          # Report templates (converted to XLSX)
│   └── security/         # Login/register templates
├── var/                   # Cache and logs
└── vendor/                # Composer dependencies
```

## Key Components

### Controllers

- **SecurityController**: Handles login, logout, and registration
- **DashboardController**: Main dashboard page
- **OAuthController**: Toggl OAuth2 connection flow
- **ReportController**: Report generation and download

### Entities

- **User**: User account with email/password authentication
- **TogglToken**: Stores OAuth2 access and refresh tokens

### Services

- **TogglApiService**: Communicates with Toggl Track API v9
- **ReportAggregationService**: Aggregates time entries by project/day
- **ReportGeneratorService**: Converts Twig templates to XLSX files

### OAuth Provider

- **TogglProvider**: Custom OAuth2 provider for Toggl Track
- **TogglResourceOwner**: Represents the authenticated Toggl user

## Data Flow

1. User logs in or registers
2. User connects Toggl account via OAuth2
3. OAuth tokens are stored in the database
4. User selects month and template for report
5. Time entries are fetched from Toggl API
6. Data is aggregated by the aggregation service
7. Template is rendered with data
8. HTML table is parsed and converted to XLSX
9. Styled XLSX file is downloaded

## Template System

Report templates are Twig files with HTML tables. The system:

1. Renders the Twig template with report data
2. Parses the resulting HTML table structure
3. Maps table elements to Excel cells
4. Applies styling (headers get blue background, borders, etc.)
5. Auto-sizes columns
6. Generates XLSX file using PhpSpreadsheet

### Available Template Variables

- `entries`: Array of aggregated time entries
- `summary`: Array of project summaries
- `total_hours`: Total hours across all entries
- `month`: Selected month (formatted)
- `start_date`: First day of month
- `end_date`: Last day of month

## Security

- Passwords hashed with Symfony's password hasher (bcrypt/argon2)
- CSRF protection on all forms
- OAuth tokens stored in database (consider encryption for production)
- Role-based access control (ROLE_USER required for most pages)
- Session-based authentication

## API Integration

The application uses Toggl Track API v9:

- Base URL: `https://api.track.toggl.com/api/v9`
- Authentication: Bearer token (OAuth2 access token)
- Endpoints used:
  - `/me` - Get current user info
  - `/me/workspaces` - List user workspaces
  - `/me/time_entries` - Get time entries with date filtering
  - `/workspaces/{id}/projects` - Get workspace projects

## Database Schema

### users
- id (serial)
- email (varchar, unique)
- roles (json)
- password (varchar)

### toggl_tokens
- id (serial)
- user_id (foreign key to users)
- access_token (text)
- refresh_token (text, nullable)
- expires_at (timestamp)
- created_at (timestamp)

## Development Workflow

1. Make changes to code
2. Clear cache if needed: `php bin/console cache:clear`
3. Update database schema: `php bin/console make:migration` then `php bin/console doctrine:migrations:migrate`
4. Test locally with Symfony server or Docker
5. Commit and push changes

## Deployment Considerations

For production deployment:

1. Set `APP_ENV=prod` in `.env`
2. Update `APP_SECRET` with a secure random string
3. Configure database with production credentials
4. Set up HTTPS/SSL
5. Consider encrypting OAuth tokens in the database
6. Enable OPcache for PHP
7. Use a reverse proxy (nginx/Apache) in front of the application
8. Set up regular database backups
9. Monitor logs in `var/log/`
10. Configure proper file permissions (www-data user)
