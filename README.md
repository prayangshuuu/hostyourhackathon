# HostYourHackathon

Free and open-source hackathon management platform built with Laravel. Supports team registration, idea submissions, judging, segments, announcements, and runs on shared hosting.

## Quick Start

```bash
git clone https://github.com/prayangshuuu/hostyourhackathon.git
cd hostyourhackathon
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Run in separate terminals:

```bash
php artisan serve        # → http://127.0.0.1:8000
npm run dev              # → Vite HMR
```

Or run everything at once:

```bash
composer dev
```

## Database Setup

```sql
-- Connect to PostgreSQL
psql -U postgres

-- Create database and user
CREATE DATABASE hostyourhackathon;
CREATE USER hostyourhackathon WITH PASSWORD 'your_password';

-- Configure defaults
ALTER ROLE hostyourhackathon SET client_encoding TO 'utf8';
ALTER ROLE hostyourhackathon SET default_transaction_isolation TO 'read committed';
ALTER ROLE hostyourhackathon SET timezone TO 'UTC';

-- Grant access
GRANT ALL PRIVILEGES ON DATABASE hostyourhackathon TO hostyourhackathon;
```

Update `.env` with your credentials:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hostyourhackathon
DB_USERNAME=hostyourhackathon
DB_PASSWORD=your_password
```

## Features

- **Hackathon management** with full CRUD and status transitions (draft → published → ongoing → ended → archived)
- **Multi-segment support** for organizing hackathon tracks/categories
- **Co-organizer assignment** — invite team members by email to help manage events
- **Role-based access control** with organizer, participant, judge, mentor, and super admin roles
- **Google OAuth** social login with Laravel Socialite
- **Email verification** and password reset flows

## License

Open-source software.

