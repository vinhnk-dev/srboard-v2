# SR Board Refactor (v2)

This repository demonstrates a **real-world architectural refactor** of a production Laravel application from legacy MVC + Repository pattern to a clean **3-layer architecture** (Controller → Service → Repository).  

The focus is on building a solid foundation for long-term maintainability and future development, rather than performance optimizations, security fixes, or full rewrites.

### Project Background
As core backend developer, I built and maintained this Laravel application from scratch through multiple iterations. It served an internal team of ~30 users with various features and modules.  

The codebase had already undergone one refactor (to MVC-like CMS structure + Repository pattern). I took it further to full 3-layer architecture to improve separation of concerns, reduce technical debt accumulation, and make adding new features easier without disrupting existing functionality.

### Goals of the Refactor
- Centralize business logic in a dedicated **Service layer** (no more scattered rules in controllers or models).
- Isolate data access in **Repositories** for easier swapping (e.g., MySQL to external API) or testing.
- Eliminate God Models by extracting rendering/formatting logic where possible.
- Apply **SOLID principles** pragmatically (especially SRP – Single Responsibility Principle).
- Adopt a **gradual and pragmatic approach**: Improve incrementally (later features benefit from lessons learned from earlier ones), follow "if it's good, don't touch it" for stable parts (e.g., rendering logic handled by previous mentor – minimal changes to avoid risk).
- Prepare the codebase for future evolution (e.g., adding DTOs, caching, events, or new patterns) without major disruptions.

### Key Architectural Changes (Before vs After)
- **Before**: Fat controllers/models, business logic mixed across layers, harder to test/maintain.
- **After**:
  - Slim controllers: Only handle HTTP requests/responses.
  - Service layer: Orchestrates business rules and workflows.
  - Repositories: Pure data operations (queries, persistence).
  - Rendering/formatting extracted to dedicated services where refactored (ongoing for remaining parts).
  - Improved clarity of responsibilities per SOLID.

### Tech Stack
- Laravel (compatible with 9/10/11/12)
- PHP 8+
- MySQL
- Tailwind CSS + Vite (for assets)
- PHPUnit (tests for critical services/business logic)

### How to Run Locally
```bash
git clone https://github.com/vinhnk-dev/srboard-v2.git
cd srboard-v2
composer install
cp .env.example .env
php artisan key:generate
# Configure .env (DB, etc.)
php artisan migrate --path=config/migrations
php artisan db:seed
php artisan serve