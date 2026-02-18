# BookIt SaaS

A multi-tenant appointment booking platform for service businesses.

## Tech Stack
- **Backend:** Laravel 11 · PHP 8.3 · PostgreSQL · Redis · GraphQL
- **Frontend:** React 18 · TypeScript · Vite · TailwindCSS
- **Infrastructure:** Docker · Github Actions CI/CD · AWS Free Tier
- **Payments:** Stripe

## Architecture

- Domain-Drive Design (DDD-lite) with Action classes
- Multi-tenant via shared database with tenant scoping
- REST API + GraphQL (Lighthouse)
- Token-based auth (Laravel Sanctum)

See [Architecture Docs](docs/architecture/ARCHITECTURE.md) for details.

## Getting Started

### Prerequisites

- Docker
- Git
- Node.js 20+ (for frontend)

### Quick Start
```bash
git clone https://github.com/ralph-sison/bookit-saas.git
cd bookit-saas
cp backend/.env.example backend/.env
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrated --seed
```

- **API:** http://localhost:8000
- **Frontend:** http://localhost:5173
- **Mailpit:** http://localhost:8025

## Project Status

- Under active development

- [x] Project setup & architecture
- [ ] Authentication & multi-tenancy
- [ ] Core domain (services, staff, availability)
- [ ] Booking system
- [ ] Stripe payment integration
- [ ] Notification system
- [ ] React frontend
- [ ] GraphQL API
- [ ] Redis caching & performance
- [ ] CI/CD & AWS deployment