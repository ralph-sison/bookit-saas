# Development Principles & Standards

## Git Workflow

- **Branch naming:** `feature/BOK-XX-short-description`, `fix/BOK-XX-description`, `chore/BOK-XX-description`
- **Commit messages:** Conventional Commits format
    - `feat: add booking creation endpoint`
    - `fix: resolve timezone bug in availability`
    - `chore: update Docker config`
    - `docs: add API endpoint documentation`
    - `test: add unit tests for CreateBookingAction`
    - `refactor: extract availability logic to service`
- **PR flow:** Feature branch -> PR -> CI passes -> Squash merge to `main`
- **No direct pushes to `main`** after initial setup

## Code Standards

### PHP / Laravel
- PSR-12 coding standard (enforced by Laravel Pint)
- `declare(strict_types=1)` in every PHP file
- PHPStan level 8 static analysis
- Pest for testing, aim for 80%+ coverage
- Action classes over service classes (single responsibility)
- FormRequest validation (never validate in controllers)
- API Resources for response transformation
- Repository pattern only when query logic is complex

### TypeScript / React
- Strict TypeScript (`strict: true`)
- ESLint + Prettier
- Functional components with hooks (no class components)
- Custom hooks for shared logic
- Feature-based folder structure
- Zod for runtime validation / schema definitions
- TanStack Query (React Query) for server state
- Zustand for client state (if needed)

### API Standards

- JSON:API-inspired response format
- Consistent error responses with error codes
- API versioning via URL prefix (`/api/v1/`)
- Rate limiting on all endpoints
- Pagination on all list endpoints
