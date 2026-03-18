# BookIt SaaS

A multi-tenant appointment booking SaaS platform built with Laravel, React, and TypeScript.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** React 18, TypeScript, Vite (coming soon)
- **Database:** PostgreSQL 16
- **Cache/Queue:** Redis 7
- **API:** REST + GraphQL (Lighthouse, planned)
- **Payments:** Stripe (planned)
- **Auth:** Laravel Sanctum
- **Infrastructure:** Docker, GitHub Actions CI/CD (planned)

## Features Implemented

### Phase 1 — Project Setup & Architecture
- Domain-Driven Design (DDD) folder structure
- Docker development environment (PHP 8.3, PostgreSQL 16, Redis 7, Nginx, Mailpit)
- Base Action and DTO classes
- Code quality tools (PHPStan level 8, Laravel Pint, Pest)
- Makefile for common commands

### Phase 2 — Authentication & Multi-tenancy
- User registration with automatic business (tenant) creation
- Login/logout with Sanctum token-based auth
- User profile endpoint
- Tenant context service with middleware resolution (header, subdomain, query param)
- Role-based access control (owner, admin, staff, client)
- UUID primary keys across all models
- Full test coverage for auth and tenant middleware

## Getting Started

### Prerequisites
- Docker & Docker Compose

### Setup
```bash
git clone https://github.com/ralph-sison/bookit-saas.git
cd bookit-saas
docker compose up -d
docker compose exec app composer install
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Running Tests
```bash
docker compose exec app php artisan test
```

### Code Quality
```bash
docker compose exec app ./vendor/bin/pint            # Code formatting
docker compose exec app ./vendor/bin/phpstan analyse  # Static analysis
```

## Architecture

- **Multi-tenancy:** Shared database with `tenant_id` discrimination and query scopes
- **DDD Structure:** Code organized by business domain (Auth, Booking, Business, Payment, Notification)
- **SOLID Principles:** Single responsibility actions, dependency injection, interface segregation

## Project Roadmap

- [x] Phase 1: Project Setup & Architecture
- [x] Phase 2: Authentication & Multi-tenancy
- [ ] Phase 3: Core Domain (Services, Staff, Availability)
- [ ] Phase 4: Booking System
- [ ] Phase 5: Payment Integration (Stripe)
- [ ] Phase 6: Notifications
- [ ] Phase 7: Frontend (React + TypeScript)
- [ ] Phase 8: GraphQL API
- [ ] Phase 9: Caching & Performance (Redis)
- [ ] Phase 10: CI/CD & Deployment (AWS)

## License

MIT