# GitHub Stars API

A Laravel-based API for managing GitHub starred repositories with custom tagging functionality.

## Features

- Fetch and sync starred repositories from GitHub
- Add custom tags to repositories
- Search repositories by tags
- Rate limiting protection
- Caching implementation
- Full test coverage

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd github-stars-api
```

2. Install dependencies:
```bash
composer install
```

3. Create and configure .env file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=github_stars
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

## API Endpoints

### Sync Starred Repositories
```
POST /api/sync-starred
Body: { "username": "github_username" }
```

### Get User's Repositories
```
GET /api/repositories?username=github_username
```

### Search Repositories by Tag
```
GET /api/repositories/search?username=github_username&tag=tag_name
```

### Add Tags to Repository
```
POST /api/repositories/{repository_id}/tags
Body: { "tags": ["tag1", "tag2"] }
```

### Remove Tags from Repository
```
DELETE /api/repositories/{repository_id}/tags
Body: { "tags": ["tag1", "tag2"] }
```

### Check GitHub API Rate Limit
```
GET /api/rate-limit
```

## Running Tests

```bash
php artisan test
```

## Rate Limiting

The API is protected by rate limiting of 60 requests per minute per IP address.

## License

This project is open-sourced software licensed under the MIT license.
