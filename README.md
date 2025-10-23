# Inviro Backend

A Laravel-based automated WhatsApp messaging system that syncs customer data from Google Sheets and sends scheduled follow-up messages via WhatsApp.

## Features

-   📊 **Google Sheets Integration** - Automatically sync customer data from spreadsheets
-   💬 **WhatsApp Messaging** - Send bulk messages via Fonnte API with multiple token rotation
-   📅 **Scheduled Messages** - Configure delay and sending intervals
-   🎯 **Message Templates** - Create and manage custom message templates
-   📈 **Dashboard Analytics** - Track customers, quantities, income, and message status
-   🔐 **JWT Authentication** - Secure API endpoints
-   📝 **API Documentation** - Auto-generated with Scramble

## Tech Stack

-   **Framework:** Laravel 12
-   **Authentication:** JWT (tymon/jwt-auth)
-   **Database:** SQLite (configurable)
-   **API Documentation:** Scramble
-   **Testing:** Pest PHP
-   **WhatsApp API:** Fonnte

## Requirements

-   PHP >= 8.2
-   Composer
-   SQLite (or MySQL/PostgreSQL)
-   Node.js & NPM (for asset compilation)

## Installation

1. **Clone the repository**

```bash
git clone <repository-url>
cd inviro-be
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. **Configure environment variables**

```env
APP_NAME=Inviro
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# WhatsApp Configuration
WHATSAPP_TOKEN=your-fonnte-token
TEAM_NUMBER=6281234567890,6289876543210

# Google Sheets
SPREADSHEET_ID=your-spreadsheet-id
SHEETS_KEY=your-google-api-key
```

5. **Create database and migrate**

```bash
touch database/database.sqlite
php artisan migrate --seed
```

6. **Start the development server**

```bash
php artisan serve
```

## Default Credentials

```
Email: admin@admin.com
Password: password
```

## API Documentation

Access the interactive API documentation at:

```
http://localhost:8000/docs
```

## Key Endpoints

### Authentication

-   `POST /api/login` - Login and get JWT token
-   `POST /api/logout` - Logout
-   `POST /api/refresh` - Refresh JWT token
-   `GET /api/me` - Get authenticated user

### Dashboard

-   `GET /api/chart` - Get weekly chart data
-   `GET /api/card/{date}` - Get daily statistics

### Customers

-   `GET /api/customers/{date}` - Get customers by date
-   `GET /api/customers/messages` - Get all message history
-   `GET /api/customers/messages/today` - Get today's messages
-   `GET /api/customer/{order_id}` - Get customer by order ID
-   `PUT /api/customer/message/{order_id}` - Edit customer's message template
-   `POST /api/customer/message/{order_id}` - Resend failed message

### Message Templates

-   `GET /api/templates` - List all templates
-   `POST /api/templates` - Create new template
-   `GET /api/templates/{id}` - Get template details
-   `PUT /api/templates/{id}` - Update template
-   `DELETE /api/templates/{id}` - Delete template

### Configuration

-   `GET /api/config/spreadsheet` - Get spreadsheet config
-   `GET /api/config/sending` - Get sending config
-   `PUT /api/config/sending` - Update sending config
-   `PUT /api/config/sync` - Update sync time
-   `PUT /api/config/link` - Update spreadsheet link

### Users

-   `GET /api/users` - List all users
-   `POST /api/users` - Create user
-   `GET /api/users/{id}` - Get user details
-   `PUT /api/users/{id}` - Update user
-   `DELETE /api/users/{id}` - Delete user

### WhatsApp Tokens

-   `GET /api/tokens` - List all tokens
-   `GET /api/tokens/{id}` - Get token details
-   `PUT /api/tokens/{id}` - Update token

## Scheduled Tasks

The application uses Laravel's task scheduler for automated operations:

```bash
# Run the scheduler
php artisan schedule:work
```

**Scheduled Tasks:**

-   `sheet:sync` - Syncs Google Sheets data every 30 seconds
-   `message:send` - Sends scheduled messages daily at 09:00

### Manual Commands

```bash
# Sync spreadsheet data
php artisan sheet:sync

# Send pending messages
php artisan message:send

# Decode spreadsheet JSON
php artisan sheet:decode
```

## Testing

Run the test suite with Pest:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthenticationTest.php

# Run with coverage
php artisan test --coverage
```

## Configuration

### Message Template Variables

Use these variables in your message templates:

-   `{nama}` - Customer name

Example:

```
Halo {nama},

Terima kasih atas pesanan Anda!
```

### Sending Configuration

-   **Days After**: Number of days after order date to send message (1-10 days)
-   **Send Interval**: Delay between messages in seconds (5-60 seconds)

### Spreadsheet Format

The Google Sheets should have the following structure:

-   Column A: Customer Name (Nama Pelanggan)
-   Column B: Phone Number (Telepon)
-   Following columns: Product names and quantities
-   Row with "Harga Satuan": Contains product prices

## Deployment

### GitHub Actions

The repository includes automated deployment workflows:

1. **Test Workflow** (`.github/workflows/test.yml`)

    - Runs on push to main branch
    - Executes Pest test suite

2. **Deploy Workflow** (`.github/workflows/deploy.yml`)
    - Triggers after successful test completion
    - Deploys to Arenhost server via SSH

### Manual Deployment

```bash
# On server
cd /path/to/project
git pull
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Structure

```
inviro-be/
├── app/
│   ├── Console/Commands/      # Artisan commands
│   ├── Exceptions/            # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/       # API controllers
│   │   ├── Requests/          # Form requests
│   │   └── Resources/         # API resources
│   ├── Models/                # Eloquent models
│   └── Traits/                # Reusable traits
├── config/                    # Configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── routes/
│   ├── api.php              # API routes
│   ├── console.php          # Console routes
│   └── web.php              # Web routes
└── tests/
    ├── Feature/             # Feature tests
    └── Unit/                # Unit tests
```

## Key Models

### Customer

Stores customer order information and message status.

**Status values:**

-   `waiting` - Pending to be sent
-   `sended` - Successfully sent
-   `failed` - Failed to send

### Message

Message templates that can be assigned to customers.

**Fields:**

-   `title` - Template name
-   `text` - Message content
-   `default` - Whether this is the default template

### WhatsappToken

Manages multiple Fonnte API tokens for load balancing.

**Fields:**

-   `token` - Fonnte API token
-   `active` - Whether the token is active
-   `used` - Currently in use flag

## License

This project is proprietary software. All rights reserved.

## Support

For support and inquiries, please contact the development team.

---

**Last Updated:** October 2025
