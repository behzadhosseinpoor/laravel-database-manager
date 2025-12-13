# Release Notes

## [Unreleased](https://github.com/behzadhosseinpoor/laravel-database-manager/compare/v1.1.1...main)

## v1.1.1 (2025-12-13)

### Fixed

- Fixed an issue where the package was overriding Laravel's global ExceptionHandler
- Restored application's default 404 and error handling behavior
- Ensured package errors are handled only within database-manager routes

### Changed

- Replaced global ExceptionHandler extension with a route-level middleware
- Normalized `database-manager.middleware` config to support string or array values
- Automatically merged package internal middleware with user-defined middlewares

### Notes

- This update does not introduce breaking changes
- Users can safely customize middlewares without affecting application-level exception handling

## v1.1.0 (2025-12-08)

### Added

- New fully redesigned confirmation modal for DROP and TRUNCATE actions  
  with complete Dark Mode support, improved hierarchy, and better UX.
- Global Axios response interceptor for unified toast notifications  
  (success → green toast, error → red toast).
- Elegant “No Data Available” empty-state component for empty query results.
- Package-level Exception Handler using `app->extend()` to isolate  
  package exceptions from the main Laravel application's handler.
- Standardized API success messages for DROP and TRUNCATE requests.

### Improved

- Significantly enhanced UI for destructive actions, including colors, spacing,  
  motion transitions, and dark‑mode readability.
- Better loading behavior using `async/await` in `getData()` and `loadOverview()`.
- Enhanced query execution logic to ensure tables reset properly after failures.
- Updated URL-building logic to ensure queries are encoded and isolated correctly.
- Better Dark Mode compatibility for Teleport-based modals.

### Fixed

- Issue where old table data was still visible after a failed query.
- Issue where modal background remained white in dark mode due to Teleport  
  escaping `#app`.
- Result state (`columns`, `rows`, `total`) not being reset correctly inside  
  `catch` blocks.
- Several minor UI inconsistencies in spacing, borders, colors and transitions.

---

## v1.0.2 (2025-12-08)

### Fixed

- Correct binary data detection via prefix
- Center align download button in table cells
- Added BinaryDownload component integration improvements

### Improved

- UI consistency for table rendering

---

## v1.0.1 (2025-12-07)

### Fixed

- Corrected handling of binary/geometry/BLOB fields
- Added automatic base64 encoding for invalid UTF-8 columns

---

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
