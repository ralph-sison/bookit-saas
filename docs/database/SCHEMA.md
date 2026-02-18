# Database Schema Design

## Multi-tenancy

Every tenant-scoped table includes a `tenant_id` column with a foreign key
to the `tenants` table. A global scope on models ensures data isolation.

## Core Tables

### tenants
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| name | varchar(255) | Business name |
| slug | varchar(255) | Unique, used for subdomain |
| owner_id | uuid | FK -> users |
| settings | jsonb | Business settings (timezone, currency, etc.) |
| subscription_status | varchar(50) | trial, active, past_due, canceled |
| stripe_customer_id | varchar(255) | Nullable |
| stripe_subscription_id | varchar(255) | Nullable |
| trial_ends_at | timestamp | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### users
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| tenant_id | uuid | FK → tenants (nullable for super-admin) |
| name | varchar(255) | |
| email | varchar(255) | Unique per tenant |
| password | varchar(255) | |
| role | varchar(50) | owner, admin, staff, customer |
| email_verified_at | timestamp | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### services
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| tenant_id | uuid | FK → tenants |
| name | varchar(255) | e.g., "Men's Haircut" |
| description | text | Nullable |
| duration_minutes | integer | e.g., 30 |
| price_cents | integer | Price in smallest currency unit |
| currency | varchar(3) | e.g., "USD" |
| is_active | boolean | Default true |
| sort_order | integer | Display ordering |
| created_at | timestamp | |
| updated_at | timestamp | |

### staff_members
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| tenant_id | uuid | FK → tenants |
| user_id | uuid | FK → users |
| display_name | varchar(255) | |
| bio | text | Nullable |
| is_active | boolean | Default true |
| created_at | timestamp | |
| updated_at | timestamp | |

### staff_schedules
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| staff_member_id | uuid | FK → staff_members |
| day_of_week | smallint | 0=Sunday, 6=Saturday |
| start_time | time | e.g., "09:00" |
| end_time | time | e.g., "17:00" |
| is_available | boolean | Default true |

### bookings
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| tenant_id | uuid | FK → tenants |
| service_id | uuid | FK → services |
| staff_member_id | uuid | FK → staff_members |
| customer_id | uuid | FK → users |
| starts_at | timestamp | Appointment start |
| ends_at | timestamp | Appointment end |
| status | varchar(50) | pending, confirmed, completed, cancelled, no_show |
| notes | text | Nullable, customer notes |
| cancelled_at | timestamp | Nullable |
| cancellation_reason | text | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### payments
| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| tenant_id | uuid | FK → tenants |
| booking_id | uuid | FK → bookings |
| stripe_payment_intent_id | varchar(255) | |
| amount_cents | integer | |
| currency | varchar(3) | |
| status | varchar(50) | pending, succeeded, failed, refunded |
| refunded_at | timestamp | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

## Indexes

- `tenants`: unique on `slug`
- `users`: unique on (`tenant_id`, `email`)
- `bookings`: index on (`tenant_id`, `staff_member_id`, `starts_at`)
- `bookings`: index on (`tenant_id`, `customer_id`)
- `bookings`: index on (`tenant_id`, `status`)
- `staff_schedules`: unique on (`staff_member_id`, `day_of_week`)