# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] - TBD

### Changed
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
- Rector as dev dependency for automated code refactoring
- Additional PHP extensions required in CI: `bcmath`, `gd`, `zip`
- PHPUnit strict testing flags: `--fail-on-warning`, `--fail-on-risky`
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

---

## [2.x.x] - Previous versions

See Git history for changes in earlier versions.