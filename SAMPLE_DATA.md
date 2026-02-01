# Sample Data for Testing

This document provides sample data structures for testing the application without needing actual Toggl data.

## Sample Time Entry Response from Toggl API

```json
[
  {
    "id": 1234567890,
    "workspace_id": 123456,
    "project_id": 789012,
    "project_name": "Client Website",
    "description": "Implemented user authentication",
    "start": "2024-01-15T09:00:00Z",
    "stop": "2024-01-15T11:30:00Z",
    "duration": 9000,
    "tags": ["development", "backend"],
    "at": "2024-01-15T11:30:00Z"
  },
  {
    "id": 1234567891,
    "workspace_id": 123456,
    "project_id": 789012,
    "project_name": "Client Website",
    "description": "Fixed responsive design issues",
    "start": "2024-01-15T14:00:00Z",
    "stop": "2024-01-15T16:00:00Z",
    "duration": 7200,
    "tags": ["development", "frontend"],
    "at": "2024-01-15T16:00:00Z"
  },
  {
    "id": 1234567892,
    "workspace_id": 123456,
    "project_id": 789013,
    "project_name": "Internal Tools",
    "description": "Updated documentation",
    "start": "2024-01-16T10:00:00Z",
    "stop": "2024-01-16T12:00:00Z",
    "duration": 7200,
    "tags": ["documentation"],
    "at": "2024-01-16T12:00:00Z"
  },
  {
    "id": 1234567893,
    "workspace_id": 123456,
    "project_id": 789012,
    "project_name": "Client Website",
    "description": "Code review",
    "start": "2024-01-16T14:00:00Z",
    "stop": "2024-01-16T15:30:00Z",
    "duration": 5400,
    "tags": ["review"],
    "at": "2024-01-16T15:30:00Z"
  }
]
```

## Sample Aggregated Data Structure

After aggregation by project and day:

```php
[
  [
    'date' => '2024-01-15',
    'project_id' => 789012,
    'project_name' => 'Client Website',
    'total_seconds' => 16200,
    'total_hours' => 4.5,
    'entries' => [
      [
        'description' => 'Implemented user authentication',
        'duration' => 9000,
        'hours' => 2.5
      ],
      [
        'description' => 'Fixed responsive design issues',
        'duration' => 7200,
        'hours' => 2.0
      ]
    ]
  ],
  [
    'date' => '2024-01-16',
    'project_id' => 789013,
    'project_name' => 'Internal Tools',
    'total_seconds' => 7200,
    'total_hours' => 2.0,
    'entries' => [
      [
        'description' => 'Updated documentation',
        'duration' => 7200,
        'hours' => 2.0
      ]
    ]
  ],
  [
    'date' => '2024-01-16',
    'project_id' => 789012,
    'project_name' => 'Client Website',
    'total_seconds' => 5400,
    'total_hours' => 1.5,
    'entries' => [
      [
        'description' => 'Code review',
        'duration' => 5400,
        'hours' => 1.5
      ]
    ]
  ]
]
```

## Sample Project Summary

After aggregation by project only:

```php
[
  [
    'project_id' => 789012,
    'project_name' => 'Client Website',
    'total_seconds' => 21600,
    'total_hours' => 6.0
  ],
  [
    'project_id' => 789013,
    'project_name' => 'Internal Tools',
    'total_seconds' => 7200,
    'total_hours' => 2.0
  ]
]
```

## Testing with Mock Data

To test the report generation without Toggl API access, you can temporarily modify the `ReportController::generate()` method to use mock data:

```php
// In ReportController::generate()
// Comment out the API call and use this instead:

$timeEntries = [
    [
        'id' => 1,
        'project_id' => 1,
        'project_name' => 'Test Project',
        'description' => 'Testing report generation',
        'start' => '2024-01-15T09:00:00Z',
        'duration' => 7200
    ],
    [
        'id' => 2,
        'project_id' => 1,
        'project_name' => 'Test Project',
        'description' => 'Another test entry',
        'start' => '2024-01-16T10:00:00Z',
        'duration' => 3600
    ]
];
```

## Sample OAuth Token Response

```json
{
  "access_token": "1a2b3c4d5e6f7g8h9i0j",
  "token_type": "Bearer",
  "expires_in": 3600,
  "refresh_token": "9i8h7g6f5e4d3c2b1a0z",
  "scope": "read_time_entries read_projects"
}
```

## Sample User Data from Toggl API

```json
{
  "id": 123456,
  "email": "user@example.com",
  "fullname": "John Doe",
  "timezone": "Europe/Prague",
  "default_workspace_id": 789012,
  "at": "2024-01-01T00:00:00Z"
}
```

## Sample Workspace Response

```json
[
  {
    "id": 789012,
    "name": "My Workspace",
    "premium": true,
    "admin": true,
    "at": "2024-01-01T00:00:00Z"
  }
]
```

## Sample Project Response

```json
[
  {
    "id": 123456,
    "workspace_id": 789012,
    "name": "Client Website",
    "active": true,
    "color": "#06aaf5",
    "at": "2024-01-01T00:00:00Z"
  },
  {
    "id": 123457,
    "workspace_id": 789012,
    "name": "Internal Tools",
    "active": true,
    "color": "#c7af14",
    "at": "2024-01-01T00:00:00Z"
  }
]
```

## Using Sample Data for Development

1. **Create a test fixture class**:
   ```php
   // src/DataFixtures/TestDataFixtures.php
   namespace App\DataFixtures;

   use App\Entity\User;
   use Doctrine\Bundle\FixturesBundle\Fixture;
   use Doctrine\Persistence\ObjectManager;
   use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

   class TestDataFixtures extends Fixture
   {
       public function __construct(
           private UserPasswordHasherInterface $passwordHasher
       ) {}

       public function load(ObjectManager $manager): void
       {
           $user = new User();
           $user->setEmail('test@example.com');
           $hashedPassword = $this->passwordHasher->hashPassword($user, 'test123');
           $user->setPassword($hashedPassword);

           $manager->persist($user);
           $manager->flush();
       }
   }
   ```

2. **Load fixtures**:
   ```bash
   composer require --dev doctrine/doctrine-fixtures-bundle
   php bin/console doctrine:fixtures:load
   ```

## Note on Duration Format

Toggl API returns duration in **seconds**:
- 3600 seconds = 1 hour
- 7200 seconds = 2 hours
- 1800 seconds = 0.5 hours

The application converts this to hours for display:
```php
$hours = $seconds / 3600;
$roundedHours = round($hours, 2);
```
