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
- **Team registration with invite links** — create teams, share invite codes, join via link
- **Window-based registration enforcement** — registration open/close dates from hackathon settings
- **Team size and solo participation rules** — min/max team size, allow_solo toggle
- **Role-based access control** with organizer, participant, judge, mentor, and super admin roles
- **Google OAuth** social login with Laravel Socialite
- **Email verification** and password reset flows
- **Draft and final idea submission** — save progress, finalize when ready
- **Time-gated submission window** with live countdown timer
- **File uploads** — PDF, PPT, PPTX with configurable size limits
- **Scoring rubric builder** — define criteria with max scores per hackathon
- **Segment-based judge assignment** — assign judges to specific tracks
- **Leaderboard with visibility control** — public/private toggle, ranked results

## License

Open-source software.

