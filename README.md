# News Aggregator - Laravel Backend API

A robust Laravel-based news aggregation system that collects articles from multiple news sources and provides a RESTful API with JWT authentication, advanced search, filtering.
## 🌟 Features

### Backend Features
- **Multi-Source News Aggregation**: Fetches from NewsAPI, The Guardian, and New York Times
- **Automated Data Collection**: Scheduled cron jobs for regular news updates
- **RESTful API**: Complete API with JWT authentication
- **Advanced Search & Filtering**: Search by keywords, filter by source, author, date range
- **Repository Pattern**: Clean architecture with repository interfaces
- **Error Handling**: Comprehensive error handling with proper HTTP responses
- **Request Validation**: Server-side validation with custom error messages

## 📋 Requirements

- PHP 8.1+
- Laravel 11.x
- MySQL 8.0+
- Composer
- Node.js (for frontend assets)

## 🚀 Quick Start

### 1. Clone & Install
```bash
git clone <repository-url>
cd news-aggregator
composer install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. News API Keys
Add your API keys to `.env`:
```env
# Get from https://newsapi.org/
NEWSAPI_KEY=your_newsapi_key

# Get from https://open-platform.theguardian.com/
GUARDIAN_API_KEY=your_guardian_key

# Get from https://developer.nytimes.com/
NYT_API_KEY=your_nyt_key
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Start the Application
```bash
php artisan serve
```

## 📊 API Documentation

### Authentication

#### Get JWT Token
```bash
POST /api/auth/token
```
Response:
```json
{
  "success": true,
  "data": {
      "access_token": "your_jwt_token",
      "token_type": "bearer",
      "expires_in": 86400
  }
}
```

### Articles Endpoints

#### Get Articles (with search/filter)
```bash
GET /api/v1/articles
Authorization: Bearer {token}
```

**Parameters:**
- `search`: Search in title, description, author
- `source[]`: Filter by source IDs (array)
- `author`: Filter by author name
- `date_from`: Start date (Y-m-d)
- `date_to`: End date (Y-m-d)
- `sort_by`: published_at|title|created_at|author
- `sort_order`: asc|desc
- `per_page`: Items per page (1-100)

#### Get Single Article
```bash
GET /api/v1/articles/{id}
Authorization: Bearer {token}
```

### Example API Calls
```bash
# Get token
curl -X POST http://your-domain.com/api/auth/token

# Search articles
curl -X GET "http://your-domain.com/api/v1/articles?search=technology&source[]=1&sort_by=published_at&sort_order=desc" \
-H "Authorization: Bearer YOUR_TOKEN"

# Get single article
curl -X GET "http://your-domain.com/api/v1/articles/123" \
-H "Authorization: Bearer YOUR_TOKEN"
```

## 🔄 Data Collection (Cron Jobs)

### Manual Collection
```bash
# Fetch from all sources manually
curl http://your-domain.com/api/cron/fetch-newsapi
curl http://your-domain.com/api/cron/fetch-guardian
curl http://your-domain.com/api/cron/fetch-nyt
```

### Automated Collection
Add to your server's crontab (`crontab -e`):
```bash
# Fetch articles every hour (staggered to avoid rate limits)
0  * * * * curl -s http://your-domain.com/api/cron/fetch-newsapi
15 * * * * curl -s http://your-domain.com/api/cron/fetch-guardian  
30 * * * * curl -s http://your-domain.com/api/cron/fetch-nyt
```

### Laravel Scheduler (Alternative)
Add single cron job:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🎨 Frontend Interface

### Access the Frontend
```
http://your-domain.com/articles
```

### Features
- **Search**: Real-time search across all articles
- **Filter**: By source, author, date range
- **Sort**: Multiple sorting options
- **Responsive**: Mobile-friendly design
- **Pagination**: Navigate through results

## 🏗️ Architecture

### Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/           # API Controllers
│   │   ├── CronJobs/      # Data fetching controllers
│   │   └── Web/           # Frontend controllers
│   ├── Requests/          # Form validation
│   ├── Resources/         # API response formatting
│   └── Middleware/        # JWT authentication
├── Models/                # Eloquent models
├── Repositories/          # Repository pattern
└── Services/              # News API services

routes/
├── api.php               # API routes
└── web.php               # Web routes

```

### Key Components

#### Repository Pattern
- `ArticleRepositoryInterface` - Contract for data access
- `ArticleRepository` - Implementation with search/filter logic

#### Services
- `NewsApiService` - NewsAPI integration
- `GuardianService` - Guardian API integration  
- `NytService` - New York Times API integration

#### Resources
- `ArticleResource` - Format single article response
- `ArticleCollection` - Format paginated articles with metadata

## 🔧 Configuration

### News APIs Configuration
Latest news fetching strategy:
- **NewsAPI**: Uses `from` parameter with latest date
- **Guardian**: Uses `from-date` parameter with latest date  
- **NYT**: Uses `begin_date` parameter with latest date

### Database Tables
- `sources` - News source information
- `articles` - Article content with foreign key `news_source_id`
- `api_tokens` - JWT token storage for authentication

## 🧪 Testing

### API Testing
```bash
# Test token generation
curl -X POST http://localhost:8000/api/auth/token

# Test articles endpoint
curl -X GET "http://localhost:8000/api/v1/articles" \
-H "Authorization: Bearer YOUR_TOKEN"

# Test search functionality
curl -X GET "http://localhost:8000/api/v1/articles?search=technology" \
-H "Authorization: Bearer YOUR_TOKEN"
```

### Cron Job Testing
```bash
# Test data fetching
curl http://localhost:8000/api/cron/fetch-newsapi
curl http://localhost:8000/api/cron/fetch-guardian
curl http://localhost:8000/api/cron/fetch-nyt
```

## 🛡️ Security

- **JWT Authentication**: Secure API access with database-stored tokens
- **Request Validation**: Server-side validation for all inputs
- **Error Handling**: Comprehensive error responses without exposing sensitive data
- **SQL Injection Protection**: Laravel's Eloquent ORM protection
- **Rate Limiting**: API endpoint protection

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure production database
3. Add production API keys
4. Enable configuration caching
5. Set up SSL/HTTPS
6. Configure web server (Apache/Nginx)

### Performance Optimization
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 📈 Monitoring

### Health Checks
- **API Health**: `GET /api/v1/articles` (requires valid token)
- **Data Collection**: Check recent articles in database
- **Frontend**: Access `http://your-domain.com/articles`

### Logs
- Application logs: `storage/logs/laravel.log`
- Cron job execution monitoring
- API request/response logging

## 🔗 API Endpoints Summary

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/token` | Get JWT token | No |
| GET | `/api/v1/articles` | Get articles with filters | Yes |
| GET | `/api/v1/articles/{id}` | Get single article | Yes |
| GET | `/api/cron/fetch-newsapi` | Fetch NewsAPI articles | No |
| GET | `/api/cron/fetch-guardian` | Fetch Guardian articles | No |
| GET | `/api/cron/fetch-nyt` | Fetch NYT articles | No |
| GET | `/articles` | Frontend interface | No |

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the API response examples

---

**Built with ❤️ using Laravel, Modern JavaScript, and RESTful API principles**
