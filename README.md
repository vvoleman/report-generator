# report-generator

Symfony (PHP 8.2+) application for generating styled XLSX reports from Toggl Track time entries.

## Features

- **User Authentication**: Secure login and registration system
- **Toggl API Token Integration**: Simple API token configuration in settings
- **Time Entry Fetching**: Retrieve time entries for any selected month
- **Data Aggregation**: Aggregate hours by project and day
- **XLSX Report Generation**: Generate styled Excel reports using PhpSpreadsheet
- **Twig-based Templates**: Extensible report templates defined in Twig HTML
- **Multi-user Support**: Each user has their own account and API token
- **Secure Token Storage**: API tokens stored securely in the database
- **Docker Environment**: Complete Docker setup for easy development

## Requirements

- PHP 8.2 or higher
- PostgreSQL 16
- Composer
- Docker and Docker Compose (for containerized setup)
- Toggl Track API token (get it from your Toggl profile)

## Installation

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/vvoleman/report-generator.git
cd report-generator
```

2. Start Docker containers:
```bash
docker-compose up -d
```

3. Install dependencies:
```bash
docker-compose exec php composer install
```

4. Run database migrations:
```bash
docker-compose exec php php bin/console doctrine:migrations:migrate
```

5. Access the application at `http://localhost:8080`

### Manual Setup

1. Install dependencies:
```bash
composer install
```

2. Configure your database connection in `.env.local`:
```
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/report_generator?serverVersion=16&charset=utf8"
```

3. Create the database:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

4. Start the Symfony development server:
```bash
symfony server:start
```

## Getting Your Toggl API Token

To use this application, you need your Toggl Track API token:

1. Log in to [Toggl Track](https://track.toggl.com)
2. Click on your profile picture in the top right corner
3. Select "Profile settings"
4. Scroll down to find your "API Token"
5. Click "Click to reveal" to show your token
6. Copy the token - you'll enter it in the application settings

## Usage

1. **Register/Login**: Create an account or login to existing account
2. **Configure API Token**: Go to Settings and enter your Toggl API token
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