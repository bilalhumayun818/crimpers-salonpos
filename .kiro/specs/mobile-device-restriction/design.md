# Design Document — Mobile Device Restriction

## Overview

This feature restricts the Laravel 11 salon POS application on mobile devices (viewport ≤ 768 px) through two independent layers:

1. **Server-side**: A new `RestrictMobileAccess` middleware intercepts authenticated requests from mobile clients and silently redirects any restricted route to the Dashboard (`/`).
2. **Client-side**: CSS `@media(max-width: 768px)` rules hide the sidebar, sidebar toggle button, sidebar backdrop, and panel navigation links, while converting Quick Action anchor links into non-navigable elements.

No database models, new routes, or user-facing UI settings are required. The feature is purely middleware + view/CSS changes.

---

## Architecture

```
HTTP Request
     │
     ▼
bootstrap/app.php  ──  web middleware group
     │                    │
     │                    ├── auth  (unchanged)
     │                    └── RestrictMobileAccess  ← NEW, appended after auth
     │
     ▼
RestrictMobileAccess::handle()
     │
     ├── isMobile($request)?  ──No──►  $next($request)  (desktop: passthrough)
     │
     └── Yes
          │
          ├── isPermittedRoute($request)?  ──Yes──►  $next($request)  (/, /login, etc.)
          │
          └── No
               │
               ▼
          redirect()->route('admin.index')  [HTTP 302]
```

On the client side, the layout template `app.blade.php` and the dashboard view `admin/index.blade.php` each contain a `@media(max-width: 768px)` block that handles all visual suppression.

---

## Components and Interfaces

### 1. `RestrictMobileAccess` Middleware

**File:** `app/Http/Middleware/RestrictMobileAccess.php`

**Responsibilities:**
- Detect mobile clients via two signals (applied in order of precedence):
  1. `Sec-CH-UA-Mobile: ?1` header — explicit browser hint, checked first.
  2. `User-Agent` regex covering common mobile browser tokens.
- Allow all requests when the client is not mobile.
- Allow mobile requests to the set of permitted routes.
- Redirect all other mobile requests to `route('admin.index')` with HTTP 302.

**Mobile detection logic:**

```php
private function isMobile(Request $request): bool
{
    // Explicit client hint takes precedence
    if ($request->header('Sec-CH-UA-Mobile') === '?1') {
        return true;
    }

    $ua = $request->userAgent() ?? '';

    return (bool) preg_match(
        '/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile Safari/i',
        $ua
    );
}
```

**Permitted route detection:**

```php
private const PERMITTED_PATHS = [
    '/',
    'login',
    'logout',
    'forgot-password',
    'reset-password',
];

private function isPermittedRoute(Request $request): bool
{
    $path = ltrim($request->path(), '/');

    foreach (self::PERMITTED_PATHS as $permitted) {
        $normalised = ltrim($permitted, '/');
        // Allow exact match or prefix for reset-password/{token}
        if ($path === $normalised || str_starts_with($path, $normalised)) {
            return true;
        }
    }

    return false;
}
```

**Full handle method:**

```php
public function handle(Request $request, Closure $next): Response
{
    if ($this->isMobile($request) && !$this->isPermittedRoute($request)) {
        return redirect()->route('admin.index');
    }

    return $next($request);
}
```

### 2. Middleware Registration — `bootstrap/app.php`

The middleware is appended to the `web` middleware group **after** `auth` so that unauthenticated requests reach Laravel's built-in `auth` redirect before the mobile check runs.

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'permission' => \App\Http\Middleware\CheckModulePermission::class,
    ]);

    // Mobile restriction — runs inside web group, after auth resolves
    $middleware->appendToGroup('web', \App\Http\Middleware\RestrictMobileAccess::class);
})
```

> **Note:** `appendToGroup('web', ...)` places the middleware at the tail of the web stack. Because the `auth` middleware runs earlier (it is applied per route group via `Route::middleware(['auth'])`), unauthenticated requests are handled by `auth` before `RestrictMobileAccess` inspects the path. This satisfies Requirement 1.8.

### 3. CSS Changes — `resources/views/layouts/app.blade.php`

A new `@media(max-width: 768px)` block is added to the existing `<style>` tag in `app.blade.php`. It supplements (not replaces) existing mobile styles already present in the file.

**New rules to add:**

```css
/* ─── Mobile Restriction: hide navigation chrome ─── */
@media(max-width: 768px) {
    /* Hide sidebar entirely — no drawer on mobile after restriction */
    #appSidebar,
    .sidebar {
        display: none !important;
    }

    /* Hide sidebar toggle button */
    #sidebarToggle {
        display: none !important;
    }

    /* Hide backdrop — prevent JS from showing it */
    #sidebar-backdrop {
        display: none !important;
    }

    /* Hide panel "View All →" / "View →" navigation links */
    .panel-link {
        display: none !important;
    }
}
```

> The `!important` declarations ensure the rules override the existing `@media(max-width:768px)` block that sets `width: 260px !important` and `transform: translateX(-100%)` on `.sidebar`, which would otherwise still reserve layout space or allow JavaScript-driven `mobile-open` class to reveal it.

### 4. Dashboard Quick Actions — `resources/views/admin/index.blade.php`

The existing `@media(max-width: 640px)` block in the dashboard already hides `.qa-pos-link`. The new requirement (Requirement 3) extends this to disable navigation on the remaining Quick Action links at ≤ 768 px.

**Approach:** Replace the four navigable `<a class="qa-link">` elements with conditionally rendered markup using Blade's `@if` on a shared `$isMobile` variable passed from the controller, **or** (preferred for zero-PHP-change) use CSS to render them non-interactive and a small Blade trick to render `<span>` wrappers on mobile.

Since the layout already uses CSS-only media queries for all other restrictions, the consistent approach is CSS-only for the visual indicator and a Blade conditional for the element swap.

**Option chosen: Blade conditional rendering** — the controller does not need to change; instead the view detects mobile server-side via the same signals as the middleware, or uses a shared view composer. To keep it simple and consistent with the existing architecture, a `@php` block inside the view evaluates the UA:

```blade
@php
    $isMobileView = str_contains(request()->header('Sec-CH-UA-Mobile', ''), '?1')
        || preg_match('/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile Safari/i',
                      request()->userAgent() ?? '');
@endphp
```

Each restricted Quick Action link then becomes:

```blade
@if($isMobileView)
<span class="qa-link qa-mobile-disabled">
@else
<a href="{{ route('appointments.index') }}" class="qa-link">
@endif
    <div class="qa-icon blue"> ... </div>
    <div class="qa-text"><strong>Appointments</strong><span>Schedule & manage</span></div>
    @if(!$isMobileView)
    <svg ... class="qa-arrow"> ... </svg>
    @endif
@if($isMobileView)
</span>
@else
</a>
@endif
```

**CSS additions for visual disabled state (in the existing `@media(max-width:640px)` block, extended to 768px):**

```css
@media(max-width: 768px) {
    .qa-mobile-disabled {
        opacity: 0.4;
        cursor: default;
        pointer-events: none;
    }
}
```

> The `panel-link` class CSS rule (hiding "View All →" links) is placed in `app.blade.php` so it applies site-wide without the dashboard view needing modification for that specific concern.

---

## Data Models

No new database models or migrations are required. This feature is entirely stateless: detection uses request headers, and state (mobile vs desktop) is computed per request.

---

## Interfaces and Contracts

### Middleware Interface

`RestrictMobileAccess` implements the standard Laravel middleware contract:

```php
interface Middleware {
    public function handle(Request $request, Closure $next): Response;
}
```

### Internal Methods (not public API)

| Method | Signature | Purpose |
|---|---|---|
| `isMobile` | `private isMobile(Request $r): bool` | Returns `true` if client is a mobile device |
| `isPermittedRoute` | `private isPermittedRoute(Request $r): bool` | Returns `true` if path is in the permitted set |

---

## Error Handling

| Scenario | Behaviour |
|---|---|
| `Sec-CH-UA-Mobile` header absent | Falls through to User-Agent check — safe default. |
| `User-Agent` header absent or empty | `preg_match` against empty string returns `false` — treated as desktop, no redirect. |
| Route `admin.index` does not exist | Laravel throws a `RouteNotFoundException` at boot time, not at runtime — caught during development. |
| JavaScript attempts to open mobile sidebar drawer | CSS `display: none !important` on `#appSidebar` prevents the element from being visible even if the `mobile-open` class is added. |
| Resize from mobile to desktop | Existing JS `resize` listener calls `closeMobileDrawer()` and restores collapsed state; CSS hides are scoped to ≤ 768px so sidebar reappears correctly at wider widths. |

---

## Testing Strategy

### Unit Tests (Example-Based)

- **Permitted routes pass through on mobile** — test each of `{/, /login, /forgot-password, /reset-password/token}` with a mobile UA, assert `$next()` is called.
- **Unauthenticated mobile request defers to auth** — assert middleware does not redirect before auth runs.
- **Desktop + any URI = passthrough** — a few representative desktop UAs against restricted routes.

### Property-Based Tests

Property tests use a PHP PBT library (e.g., [eris](https://github.com/giorgiosironi/eris)) or Laravel's built-in test helpers with randomised data.

- **Property 1** — Generate random restricted URIs (anything not in permitted set) and random mobile UA strings from each pattern token (Android, iPhone, iPad, iPod, BlackBerry, IEMobile, Opera Mini, Mobile Safari). Assert 302 redirect to `/` for all combinations. Run ≥ 100 iterations.  
  Tag: `Feature: mobile-device-restriction, Property 1: Mobile restricted route always redirects`

- **Property 2** — Generate random URIs and random desktop UA strings (no mobile tokens). Assert `$next()` always called. Run ≥ 100 iterations.  
  Tag: `Feature: mobile-device-restriction, Property 2: Desktop requests always pass through unchanged`

- **Property 3** — Generate `$isMobileView = true` and random Quick Action data sets; render the dashboard Blade partial in isolation; assert zero `<a href=` elements within `.qa-grid` (excluding POS which is already absent). Run ≥ 100 iterations.  
  Tag: `Feature: mobile-device-restriction, Property 3: Quick Action links are non-navigable on mobile`

### Smoke / Structural Tests

- `#appSidebar`, `.sidebar`, `#sidebarToggle`, `#sidebar-backdrop`, and `.panel-link` all have `display: none` inside a `@media(max-width: 768px)` rule in `app.blade.php`.
- All new CSS rules are scoped exclusively inside `@media(max-width: 768px)` blocks — no bare selectors added.
- `RestrictMobileAccess` is registered in `bootstrap/app.php`.

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Mobile restricted route always redirects

*For any* authenticated HTTP request (any method: GET, POST, PUT, PATCH, DELETE) from a Mobile Device (either `Sec-CH-UA-Mobile: ?1` or a User-Agent string matching the mobile regex) to a URI that is not in the permitted set `{/, /login, /logout, /forgot-password, /reset-password/*}`, the `RestrictMobileAccess` middleware SHALL return an HTTP 302 redirect response pointing to the Dashboard route (`admin.index`).

**Validates: Requirements 1.1, 1.2, 1.6, 1.7**

---

### Property 2: Desktop requests always pass through unchanged

*For any* HTTP request (any method, any URI) whose `Sec-CH-UA-Mobile` header is absent or not `?1` AND whose User-Agent string does not match the mobile regex, the `RestrictMobileAccess` middleware SHALL call `$next($request)` and return its response without modification.

**Validates: Requirements 1.4, 5.1, 5.2**

---

### Property 3: Quick Action links are non-navigable on mobile

*For any* rendering of `resources/views/admin/index.blade.php` where `$isMobileView` is `true`, none of the Quick Action elements for Appointments, Staff Management, Inventory, and Reports SHALL be rendered as `<a>` elements with `href` attributes — they SHALL be rendered as non-navigable `<span>` elements.

**Validates: Requirements 3.1, 3.2**
