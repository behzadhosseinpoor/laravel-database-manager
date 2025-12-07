# Release Notes

## [Unreleased](https://github.com/behzadhosseinpoor/laravel-database-manager/compare/v1.0.1...main)

## v1.0.1 (2025-12-07)

### Fixed

- Corrected handling of binary/geometry/BLOB fields
- Added automatic base64 encoding for invalid UTF-8 columns

## v1.0.0 (2025-12-07)

### Added

- Full Query Runner with:
    - Safe SQL execution (no multi‑statement)
    - Paginated SELECT results
    - Collapsible SQL editor with animation
    - Integrated DataTable with isolated pagination
- Structure View (columns + indexes)
- Browse table with sorting, pagination, improved NULL ordering
- Database overview page
- Connection switching system with session-based persistence
- Full SPA UI using Vue Router
- Dark/Light theme support
- New config file: config/database-manager.php
- Published assets and stubs
- Initial documentation (README, config, usage)
