# BookIt SaaS - Architecture Documentation

## Overview

BookIt is a multi-tenant SaaS appointment booking platform for service businesses
(salons, clinics, consultants, fitness trainers). Built with Laravel (API) and
React + Typescript (SPA frontend).

## Architecture Style

- **Backend:** Laravel as a stateless JSON API (no Blade views)
- **Frontend:** React SPA with TypeScript, communicating via REST + GraphQL
- **Multi-tenancy:** Shared database with `tenant_id` scoping (not separate DBs)
- **Tenant identification:** Subdomain-based (e.g., `acme.bookit.com`)

## Domain Model (DDD-lite)

We organize backend code by business_domain, not by technical layer:

app/
├── Domain/
│   ├── Auth/           # Registration, login, password reset
│   ├── Tenant/         # Organization/business management
│   ├── Booking/        # Appointments, calendar, availability
│   ├── Service/        # Service catalog, pricing, duration
│   ├── Staff/          # Staff profiles, schedules, assignments
│   ├── Customer/       # Client profiles, history
│   ├── Payment/        # Stripe integration, invoices
│   └── Notification/   # Email, SMS reminders
├── Http/               # Controllers, Middleware, FormRequests
├── Infrastructure/     # Repository implementations, external APIs
└── Support/            # Shared traits, helpers, value objects

Each domain contains:
- **Models** - Eloquent models
- **Actions** - Single-responsibility use-case classes (replaces fat services)
- **DTOs** - Data Transfer Objects for passing data between layers
- **Events** - Domain events
- **Policies** - Authorization logic
- **Enums** - Status types, roles, etc.

## Design Principles

| Principle | How we Apply It |
|-----------|-----------------|
| **Single Responsibility** | One Action class per use case (e.g.,
`CreateBookingAction`) |
| **Open/Closed** | Strategy pattern for payment provides, notifcation
channels |
| **Dependency Inversion** | Interfaces in Domain, implementations in
Infrastructure |
| **KISS** | Start simple, refactor when complexity demans it |
| **YAGNI** | No premature abstractions - build what the feature requires |

## Tech Stack

| Layer | Technology | Why |
|-------|------------|-----|
| Backend API | Laravel 11, PHP 8.3 | Robust, mature, excellent for SaaS |
| Frontend SPA | React 18, TypeScript, Vite | Type-safe, large ecosystem |
| Database | PostgreSQL 16 | JSONB, better concurrency, full-text search |
| Cache / Queue | Redis 7 | Fast caching, session store, job queue broker |
| API Style | REST + GraphQL (Lighthouse) | REST from simple CRUD, GraphQL for
complex queries |
| Payments | Stripe | Industry standard, excellent API |
| Auth | Laravel Sanctum | Token-based API auth, SPA cookie auth |
| Permissions | spatie/laravel-permission | Battle-tested RBAC |
| Testing | Pest (PHP), Vitest (TS) | Modern, expressive syntax |
| CI/CD | GitHub Actions | Integrated with repo, free for public repos |
| Hosting | AWS Free Tier | EC2, RDS, S3, ElasticCache |
| Containers | Docker + Docker Compose | Consistent dev/prod environments |
