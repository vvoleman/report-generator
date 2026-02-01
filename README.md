# report-generator

Symfony (PHP 8.2+) application for generating styled XLSX reports from Toggl Track time entries.

## Features

- **User Authentication**: Secure login and registration system
- **Toggl OAuth2 Integration**: Connect your Toggl Track account securely via OAuth2
- **Time Entry Fetching**: Retrieve time entries for any selected month
- **Data Aggregation**: Aggregate hours by project and day
- **XLSX Report Generation**: Generate styled Excel reports using PhpSpreadsheet
- **Twig-based Templates**: Extensible report templates defined in Twig HTML
- **Multi-user Support**: Each user has their own account and Toggl connection
- **Secure Token Storage**: OAuth tokens stored encrypted in the database
- **Docker Environment**: Complete Docker setup for easy development

## Requirements

- PHP 8.2 or higher
- PostgreSQL 16
- Composer
- Docker and Docker Compose (for containerized setup)

## Installation

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/vvoleman/report-generator.git
cd report-generator
```

2. Copy `.env` file and configure your Toggl OAuth credentials:
```bash
cp .env .env.local
# Edit .env.local and add your Toggl OAuth credentials
```

3. Start Docker containers:
```bash
docker-compose up -d
```

4. Install dependencies:
```bash
docker-compose exec php composer install
```

5. Run database migrations:
```bash
docker-compose exec php php bin/console doctrine:migrations:migrate
```

6. Access the application at `http://localhost:8080`

### Manual Setup

1. Install dependencies:
```bash
composer install
```

2. Configure your database connection in `.env.local`:
```
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/report_generator?serverVersion=16&charset=utf8"
```

3. Configure Toggl OAuth credentials in `.env.local`:
```
OAUTH_TOGGL_CLIENT_ID=your_client_id
OAUTH_TOGGL_CLIENT_SECRET=your_client_secret
```

4. Create the database:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Start the Symfony development server:
```bash
symfony server:start
```

## Toggl OAuth Setup

To use this application, you need to register an OAuth application with Toggl Track:

1. Go to [Toggl Track Developer Portal](https://track.toggl.com/profile)
2. Navigate to API Token section
3. Create a new OAuth application
4. Set the redirect URI to: `http://localhost:8080/oauth/check/toggl` (or your domain)
5. Copy the Client ID and Client Secret to your `.env.local` file

## Usage

1. **Register/Login**: Create an account or login to existing account
2. **Connect Toggl**: Click "Connect Toggl Account" on the dashboard
3. **Generate Report**: 
   - Navigate to Reports page
   - Select a month
   - Choose a template (Default, Detailed, or Summary)
   - Click "Generate Report"
4. **Download**: Your XLSX report will be downloaded automatically

## Report Templates

The application includes three built-in templates:

- **Default**: Time entries grouped by project and day with totals
- **Detailed**: Includes individual entry descriptions and granular data
- **Summary**: Project-level aggregation for quick overviews

### Creating Custom Templates

Create a new Twig template in `templates/reports/your_template.html.twig`:

```twig
<table>
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
        </tr>
    </thead>
    <tbody>
        {% for entry in entries %}
        <tr>
            <td>{{ entry.date }}</td>
            <td>{{ entry.total_hours }}</td>
        </tr>
        {% endfor %}
    </tbody>
</table>
```

The HTML table will be automatically converted to a styled XLSX file.

## Architecture

### Components

- **Entities**: User, TogglToken (Doctrine ORM)
- **OAuth Provider**: Custom Toggl OAuth2 provider
- **Services**:
  - `TogglApiService`: Interacts with Toggl Track API
  - `ReportAggregationService`: Aggregates time entry data
  - `ReportGeneratorService`: Converts Twig templates to XLSX
- **Controllers**: Security, Dashboard, OAuth, Report

### Security

- Passwords are hashed using Symfony's password hasher
- OAuth tokens are stored in the database
- CSRF protection on all forms
- Role-based access control

## Development

### Running Tests

```bash
php bin/phpunit
```

### Clearing Cache

```bash
php bin/console cache:clear
```

### Database Management

```bash
# Create migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Reset database
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Docker Services

- **php**: PHP 8.2-FPM with all required extensions
- **nginx**: Web server (port 8080)
- **database**: PostgreSQL 16 (port 5432)

## License

MIT License

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.