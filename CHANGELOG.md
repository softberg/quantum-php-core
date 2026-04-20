## [3.0.0] - TBD

### Changed
- **BREAKING:** Refactored app bootstrapping and DI ownership model (#373):
  - Introduced `AppContext` as the central execution state holder (mode, baseDir, DiContainer, Environment, Config, Request, Response, Routes)
  - Split `Di` into a static facade delegating to an instance-based `DiContainer`, one container per application execution
  - Added `Di::set()`, `Di::has()`, and `Di::isRegistered()` for explicit service management
  - `Di::get()` now enforces a strict contract: throws if the dependency is not explicitly registered (no more implicit auto-registration)
  - Introduced `BootPipeline` and `BootStageInterface` for explicit, ordered boot sequences
  - Extracted boot stages: `LoadHelpersStage`, `LoadEnvironmentStage`, `LoadAppConfigStage`, `SetupErrorHandlerStage`, `InitHttpStage`, `InitDebuggerStage`
  - Removed `AppTrait`; all adapter boot logic moved to pipeline stages and focused private methods in `WebAppTrait`/`ConsoleAppTrait`
- **BREAKING:** Converted `Request` and `Response` from static facades to instance-based classes (#454):
  - Merged `HttpRequest`/`HttpResponse` wrapper layer directly into `Request`/`Response`
  - All request/response access now goes through `request()` and `response()` helper functions
- **BREAKING:** Removed all static singleton patterns from core services (#381, #382):
  - Migrated all 12 first-party factories (Auth, Archive, Cache, Captcha, Cryptor, FileSystem, Lang, Logger, Mailer, Renderer, Session, View) from static instance caches to DI-managed lifetimes
  - Migrated service singletons to DI ownership: Cookie, Config, Environment, Server, AssetManager, Csrf, Database, MailTrap, Debugger, ViewCache, ErrorHandler, HookManager, ModuleLoader
- **BREAKING:** `Environment` class is no longer a static singleton (#456):
  - Uses `Dotenv::createArrayBacked()` for isolated, deterministic env loading
  - New `environment()` helper function and shorthand check methods: `isProduction()`, `isTesting()`, `isStaging()`, `isDevelopment()`, `isLocal()`
  - `env()` helper now delegates through `environment()->getValue()`

- **BREAKING:** Minimum PHP version requirement raised from 7.3 to 7.4
- Modernized codebase with PHP 7.4+ syntax using Rector:
  - Array destructuring: `list()` → `[]`
  - Arrow functions for simple closures
  - Null coalescing assignment operator (`??=`)
  - Class constant references using `::class`
  - Secure random number generation with `random_int()`
  - Modern `setcookie()` array syntax
- Updated CI pipeline to test against PHP 7.4, 8.0, and 8.1
- CI now fails on PHP warnings and deprecations for stricter quality control
- Added `declare(strict_types=1)` to all Exception classes for improved type safety

- **BREAKING:** Refactored routing system internals:
  - Routes are now represented as first-class objects (`Route`, `RouteCollection`, `MatchedRoute`)
  - Introduced `RouteBuilder` as a fluent DSL interpreter and route composition engine
  - Introduced `PatternCompiler` for centralized route pattern compilation
  - Introduced `RouteDispatcher` for explicit controller / closure dispatching
  - Route matching now produces a `MatchedRoute` object containing the route and extracted parameters
  - Routing is executed once per request; matched route is stored on the `Request`
  - Global route helper functions (`current_*`, `route_*`) now rely on `MatchedRoute` instead of legacy static route state
  - Middleware ordering is strictly prepend-based (later middleware wraps earlier)
  - Nested route groups are no longer supported
  - Route name uniqueness is now enforced at build time

- **BREAKING:** Controller resolution behavior changed:
  - Routes no longer implicitly resolve controller class names via legacy `RouteController` logic
  - Controllers are now instantiated directly based on the handler defined in the route
  - Projects relying on legacy controller resolution or `RouteController` static state must update accordingly

- **BREAKING:** Refactored model architecture:
  - Introduced a base Model class for non-database models
  - Refactored QtModel into DbModel with persistence-only responsibilities
  - Database fetch methods (first(), findOne(), findOneBy(), get()) now return new model instances instead of mutating existing ones
  - Calling create() resets model state to ensure clean inserts
  - Database-generated primary keys are now synced into the model after save()
  - Models are hydrated only on fetch operations, not implicitly via getters
- `DbModel::save()` now automatically applies timestamps when the model uses the `HasTimestamps` trait
- `SoftDeletes` now uses model timestamp format when available (datetime/unix) for `deleted_at`

### Fixed
- PHP 8.1 compatibility: Fixed null parameter deprecations in `explode()`, `parse_url()`, and `str_replace()`
- PHP 8.4 forward compatibility: Fixed implicitly nullable parameters in database and auth adapters
- PHP 8.4 forward compatibility: Added missing type hint to `SleekDbal::__get()` magic method
- Fixed deprecated `E_STRICT` constant usage in test bootstrap
- Fixed cURL error message assertions for cross-version compatibility

### Added
- `AppContext` class representing the runtime identity of a single application execution
- `DiContainer` instance-based dependency injection container, isolated per execution
- `BootPipeline` and `BootStageInterface` for declarative, ordered boot sequences
- `environment()` global helper and `Environment` shorthand methods (`isProduction()`, `isTesting()`, etc.)
- Lazy registration guards (`Di::register()` + `Di::isRegistered()`) at all DI call sites for explicit dependency management
- Rector as dev dependency for automated code refactoring
- Additional PHP extensions required in CI: `bcmath`, `gd`, `zip`
- PHPUnit strict testing flags: `--fail-on-warning`, `--fail-on-risky`

- **Routing test coverage**:
  - Comprehensive unit tests for route building, grouping, middleware ordering, caching, and naming
  - Unit tests for route helpers and request–route integration
  - Unit tests for dispatcher behavior with controller and closure routes

- **Cron Scheduler**: New CLI command `php qt cron:run` for running scheduled tasks
  - Task definition via PHP files in `cron/` directory
  - Cron expression parsing using `dragonmantank/cron-expression` library
  - File-based task locking to prevent concurrent execution
  - Comprehensive logging of task execution and errors
  - Support for force mode and specific task execution
  - Automatic cleanup of stale locks (older than 24 hours)
  - Full documentation in `docs/cron-scheduler.md`

- **Opt-in Model Timestamps**: Introduced `HasTimestamps` trait for `DbModel`:
  - Automatically sets `created_at` on insert
  - Automatically sets `updated_at` on insert and update
  - Supports custom timestamp column names via model constants (`CREATED_AT`, `UPDATED_AT`)
  - Supports datetime and unix timestamp formats via `TIMESTAMP_TYPE`

### Removed
- Support for PHP 7.3 and earlier versions
- Legacy routing static state and implicit controller resolution via `RouteController`
- `AppTrait` — replaced by boot pipeline stages and adapter-specific traits
- Static singleton patterns from all core services and factories
- `Environment::getInstance()` static singleton accessor
- `HttpRequest`/`HttpResponse` static facade wrapper classes (merged into `Request`/`Response`)
- Implicit auto-registration in `Di::get()` — all dependencies must be explicitly registered
- `RegisterCoreDependenciesStage` and `dependencies.php` — replaced by lazy registration at call sites
