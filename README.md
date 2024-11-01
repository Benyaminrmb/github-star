# Laravel GitHub Stars Manager

A Laravel-based API service that helps developers manage their GitHub starred repositories with additional features like custom tagging and advanced search capabilities.

## 🚀 Features

- OAuth2 GitHub authentication
- Automatic syncing of starred repositories
- Custom tagging system for better organization
- Repository search functionality
- Protected API endpoints with sanctum authentication
- GitHub API rate limit monitoring


## ⚡ Quick Installation

1. Clone the repository
```bash
git clone https://github.com/Benyaminrmb/github-star
cd github-star
```

2. Install PHP dependencies
```bash
composer install
```

3. Set up environment variables
```bash
cp .env.example .env
```

4. Configure your `.env` file with:
    - Database credentials
    - GitHub OAuth credentials
   ```
   GITHUB_CLIENT_ID=your_client_id
   GITHUB_CLIENT_SECRET=your_client_secret
   GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback
   ```

5. Generate application key
```bash
php artisan key:generate
```

6. Run database migrations
```bash
php artisan migrate
```

## 🔑 Authentication

### GitHub OAuth Authentication

The application uses GitHub OAuth for authentication. To authenticate:

1. Navigate to `/auth/github` in your browser
2. Authorize the application
3. You'll be redirected to the callback URL with your access token

### API Authentication

All protected endpoints require a valid Bearer token in the Authorization header:
```
Authorization: Bearer <your-token>
```

## 📡 API Endpoints

### Authentication
```
POST /api/auth/login-by-email    // Email-based authentication
POST /api/auth/logout            // Logout (requires authentication)
```

### GitHub Integration
```
GET /api/sync-starred           // Sync starred repositories from GitHub
GET /api/rate-limit            // Check GitHub API rate limit status
```

### Repository Management
```
GET    /api/repositories              // List all repositories
GET    /api/repositories/search       // Search repositories
POST   /api/repositories/{id}/tags    // Add tags to repository
DELETE /api/repositories/{id}/tags    // Remove tags from repository
```

## 🔒 Security

- All API endpoints (except authentication) are protected with Laravel Sanctum
- GitHub token validation middleware
- Rate limiting protection

## 💡 Usage Examples

### Adding Tags to Repository
```bash
curl -X POST \
  'http://localhost:8000/api/repositories/123/tags' \
  -H 'Authorization: Bearer your-token' \
  -H 'Content-Type: application/json' \
  -d '{
    "tags": ["frontend", "vue", "javascript"]
}'
```

### Searching Repositories
```bash
curl -X GET \
  'http://localhost:8000/api/repositories/search?username=benyaminrmb&tags=frontend' \
  -H 'Authorization: Bearer your-token'
```

## ⚙️ Development

### Running Tests
```bash
php artisan test
```


## 📝 License

This project is open-sourced software licensed under the MIT license.

## 📫 Support

If you encounter any problems or have questions, please open an issue in the GitHub repository.
