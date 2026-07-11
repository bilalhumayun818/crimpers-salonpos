# Implementation Plan: Mobile Device Restriction

## Overview

Implement mobile device access restriction through two independent layers: a server-side Laravel 11 middleware that redirects mobile clients away from restricted routes, and client-side CSS/Blade changes that hide navigation chrome and disable Quick Action links on the Dashboard. No new database models or routes are required.

## Tasks

- [x] 1. Create `RestrictMobileAccess` middleware
  - [x] 1.1 Implement the middleware class at `app/Http/Middleware/RestrictMobileAccess.php`
    - Add private `isMobile(Request $request): bool` method — check `Sec-CH-UA-Mobile: ?1` header first, then User-Agent regex covering Android, iPhone, iPad, iPod, BlackBerry, IEMobile, Opera Mini, Mobile Safari
    - Add private `isPermittedRoute(Request $request): bool` method — match against constant `PERMITTED_PATHS = ['/', 'login', 'logout', 'forgot-password', 'reset-password']`, allowing prefix matching for `reset-password/{token}`
    - Implement `handle(Request $request, Closure $next): Response` — redirect to `route('admin.index')` (HTTP 302) only when both `isMobile()` is true and `isPermittedRoute()` is false; otherwise call `$next($request)`
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.6, 1.7, 1.8_

  - [ ]* 1.2 Write property test for `RestrictMobileAccess` — Property 1: Mobile restricted route always redirects
    - **Property 1: Mobile restricted route always redirects**
    - Generate random restricted URIs (not in permitted set) × random mobile UA tokens (Android, iPhone, iPad, iPod, BlackBerry, IEMobile, Opera Mini, Mobile Safari); assert HTTP 302 redirect to `/` for all combinations (≥ 100 iterations)
    - Also assert redirect when `Sec-CH-UA-Mobile: ?1` is set, regardless of UA string
    - **Validates: Requirements 1.1, 1.2, 1.6, 1.7**

  - [ ]* 1.3 Write property test for `RestrictMobileAccess` — Property 2: Desktop requests always pass through unchanged
    - **Property 2: Desktop requests always pass through unchanged**
    - Generate random URIs × random desktop UA strings (no mobile tokens); assert `$next($request)` is called and no redirect is issued for all combinations (≥ 100 iterations)
    - **Validates: Requirements 1.4, 5.1, 5.2**

  - [ ]* 1.4 Write unit tests for permitted route pass-through and unauthenticated request handling
    - Test each permitted path (`/`, `login`, `forgot-password`, `reset-password/sometoken`) with a mobile UA — assert `$next($request)` is called (no redirect)
    - Test a mobile request with no authenticated session — assert middleware does not redirect (defers to auth)
    - _Requirements: 1.3, 1.5, 1.8_

- [x] 2. Register middleware in `bootstrap/app.php`
  - [x] 2.1 Append `RestrictMobileAccess` to the `web` middleware group in `bootstrap/app.php`
    - Use `$middleware->appendToGroup('web', \App\Http\Middleware\RestrictMobileAccess::class)` inside the `withMiddleware` callback, after the existing `permission` alias registration
    - Confirm placement is after `auth` so unauthenticated requests are handled by `auth` first
    - _Requirements: 1.5_

- [x] 3. Checkpoint — Verify server-side restriction is wired
  - Ensure all middleware tests pass, ask the user if questions arise.

- [x] 4. Add mobile CSS rules to `resources/views/layouts/app.blade.php`
  - [x] 4.1 Add `@media(max-width: 768px)` rules to the existing `<style>` block in `app.blade.php`
    - Hide `#appSidebar` and `.sidebar` with `display: none !important`
    - Hide `#sidebarToggle` with `display: none !important`
    - Hide `#sidebar-backdrop` with `display: none !important`
    - Hide `.panel-link` elements with `display: none`
    - Ensure all new rules are scoped inside `@media(max-width: 768px)` — no bare selectors
    - _Requirements: 2.1, 2.2, 2.3, 4.1, 5.3_

  - [ ]* 4.2 Write structural tests for CSS rules in `app.blade.php`
    - Assert `#appSidebar`, `.sidebar`, `#sidebarToggle`, `#sidebar-backdrop`, and `.panel-link` all appear inside a `@media(max-width: 768px)` block in the rendered or raw blade file
    - Assert no new bare (non-media-query-wrapped) selectors for these IDs/classes are introduced
    - _Requirements: 2.1, 2.2, 2.3, 4.1, 5.3_

- [x] 5. Update Dashboard view for mobile Quick Action rendering
  - [x] 5.1 Add `$isMobileView` detection block and update Quick Action markup in `resources/views/admin/index.blade.php`
    - Add `@php` block at the top of the relevant section to evaluate `$isMobileView` using `Sec-CH-UA-Mobile` header and UA regex (same logic as middleware)
    - Replace the four `<a class="qa-link">` elements (Appointments, Staff Management, Inventory, Reports) with Blade conditionals: render `<span class="qa-link qa-mobile-disabled">` (no `href`, no arrow SVG) when `$isMobileView` is true; render original `<a href="...">` with arrow SVG when false
    - Extend the existing `@media(max-width: 640px)` block (or add a new `@media(max-width: 768px)` block) to add `.qa-mobile-disabled { opacity: 0.4; cursor: default; pointer-events: none; }`
    - Confirm POS Terminal quick action continues to be hidden via the existing `.qa-pos-link { display: none }` rule (no change needed)
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ]* 5.2 Write property test for Dashboard view — Property 3: Quick Action links are non-navigable on mobile
    - **Property 3: Quick Action links are non-navigable on mobile**
    - Render `resources/views/admin/index.blade.php` with `$isMobileView = true` and random Quick Action datasets; assert zero `<a href=` elements exist within `.qa-grid` for Appointments, Staff Management, Inventory, and Reports links (≥ 100 iterations)
    - **Validates: Requirements 3.1, 3.2**

  - [ ]* 5.3 Write unit tests for desktop Quick Action rendering
    - Render the dashboard view with `$isMobileView = false`; assert all four Quick Action `<a href="...">` elements are present with correct `href` values and arrow SVGs
    - _Requirements: 3.4_

- [x] 6. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- The middleware is entirely stateless — no database or session changes
- CSS `!important` is required on sidebar/toggle/backdrop rules to override the existing `@media(max-width:768px)` block that sets `width: 260px !important` on `.sidebar`
- Property tests require a PHP PBT library (e.g., [eris](https://github.com/giorgiosironi/eris)) or equivalent; confirm availability before running
- Desktop experience (viewport > 768px) must remain 100% unchanged

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["2.1", "1.2", "1.3", "1.4"] },
    { "id": 2, "tasks": ["4.1", "5.1"] },
    { "id": 3, "tasks": ["4.2", "5.2", "5.3"] }
  ]
}
```
