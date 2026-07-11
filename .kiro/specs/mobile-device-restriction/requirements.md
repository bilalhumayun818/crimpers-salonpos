# Requirements Document

## Introduction

This feature restricts application access on mobile devices (viewport width ≤ 768 px). On mobile, authenticated users may only view the Login page and the Dashboard. All other routes are silently redirected to the Dashboard by a dedicated PHP middleware. The Dashboard itself suppresses navigation links and Quick Action cards that lead to restricted modules. The application sidebar and its toggle button are completely hidden on mobile. No changes apply to desktop or large-screen experiences.

## Glossary

- **MobileRestrictionMiddleware**: A new Laravel middleware class (`App\Http\Middleware\RestrictMobileAccess`) that inspects the `User-Agent` string and `Sec-CH-UA-Mobile` hint to determine mobile access and redirects non-permitted routes to the Dashboard.
- **Mobile Device**: Any HTTP client whose request carries a `User-Agent` string that matches a known mobile pattern OR whose `Sec-CH-UA-Mobile` header value is `?1`, resulting in a detected viewport of ≤ 768 px equivalent.
- **Restricted Route**: Any authenticated application route whose URI is not `/` (Dashboard) and is not a login or password-reset route.
- **Permitted Route**: The root Dashboard route (`/`) and all unauthenticated routes (`/login`, `/forgot-password`, `/reset-password/{token}`).
- **Quick Action Link**: An `<a>` element rendered inside the Quick Actions section of `resources/views/admin/index.blade.php` that navigates to a module other than Dashboard.
- **Sidebar**: The `<div id="appSidebar">` element rendered via `resources/views/layouts/sidebar.blade.php` and included in `resources/views/layouts/app.blade.php`.
- **Sidebar Toggle Button**: The `<button id="sidebarToggle">` element in the top header of `resources/views/layouts/app.blade.php`.
- **Desktop**: Any HTTP client not classified as a Mobile Device.

---

## Requirements

### Requirement 1 — Server-Side Mobile Route Restriction

**User Story:** As a mobile user, I want the application to automatically redirect me to the Dashboard when I attempt to access a restricted route, so that I cannot reach modules that are not designed for mobile use.

#### Acceptance Criteria

1. WHEN a Mobile Device makes an authenticated GET request to a Restricted Route, THE MobileRestrictionMiddleware SHALL redirect the client to the Dashboard route (`/`) with an HTTP 302 response.
2. WHEN a Mobile Device makes an authenticated POST, PUT, PATCH, or DELETE request to a Restricted Route, THE MobileRestrictionMiddleware SHALL redirect the client to the Dashboard route (`/`) with an HTTP 302 response.
3. WHEN a Mobile Device makes a request to a Permitted Route, THE MobileRestrictionMiddleware SHALL pass the request to the next middleware without redirection.
4. WHEN a Desktop client makes a request to any route, THE MobileRestrictionMiddleware SHALL pass the request to the next middleware without any modification.
5. THE MobileRestrictionMiddleware SHALL be registered in `bootstrap/app.php` (Laravel 11 style) and applied within the authenticated middleware group so that unauthenticated requests are not intercepted before the `auth` middleware runs.
6. WHEN the `Sec-CH-UA-Mobile` request header is present with value `?1`, THE MobileRestrictionMiddleware SHALL classify the client as a Mobile Device regardless of the `User-Agent` header value.
7. WHEN the `User-Agent` header matches a regex pattern covering common mobile browsers (Android, iPhone, iPad, iPod, BlackBerry, IEMobile, Opera Mini, Mobile Safari), THE MobileRestrictionMiddleware SHALL classify the client as a Mobile Device.
8. IF a Mobile Device request is unauthenticated, THEN THE MobileRestrictionMiddleware SHALL pass the request to the next middleware and allow the `auth` middleware to handle the redirect to login.

---

### Requirement 2 — Sidebar Hidden on Mobile

**User Story:** As a mobile user, I want the sidebar to be invisible and inaccessible, so that I am not presented with navigation options for modules I cannot access.

#### Acceptance Criteria

1. WHILE the viewport width is ≤ 768 px, THE Sidebar SHALL have CSS property `display: none` applied via a `@media(max-width: 768px)` rule in `resources/views/layouts/app.blade.php`.
2. WHILE the viewport width is ≤ 768 px, THE Sidebar toggle button (`#sidebarToggle`) SHALL have CSS property `display: none` applied via a `@media(max-width: 768px)` rule in `resources/views/layouts/app.blade.php`.
3. WHILE the viewport width is ≤ 768 px, THE sidebar backdrop overlay (`#sidebar-backdrop`) SHALL have CSS property `display: none` applied via a `@media(max-width: 768px)` rule so that it cannot appear even if JavaScript attempts to show it.
4. WHILE the viewport width is greater than 768 px, THE Sidebar, Sidebar toggle button, and sidebar backdrop SHALL retain their existing behavior and appearance without modification.

---

### Requirement 3 — Dashboard Quick Action Links Disabled on Mobile

**User Story:** As a mobile user, I want the Dashboard Quick Action links that lead to restricted modules to be absent from the page, so that I cannot accidentally navigate to an inaccessible module.

#### Acceptance Criteria

1. WHILE the viewport width is ≤ 768 px, THE Dashboard view (`resources/views/admin/index.blade.php`) SHALL render the Quick Action links (Appointments, Staff Management, Inventory, Reports) without anchor `href` attributes, replacing them with non-navigable `<span>` or `<div>` elements, so that touch events produce no navigation.
2. WHILE the viewport width is ≤ 768 px, THE Dashboard view SHALL visually indicate that Quick Action elements are non-interactive by applying a reduced-opacity CSS rule (opacity ≤ 0.5) and a `cursor: default` style to those elements.
3. WHILE the viewport width is ≤ 768 px, THE Dashboard view SHALL omit the POS Terminal Quick Action link entirely, consistent with the existing `.qa-pos-link { display: none }` rule already present.
4. WHILE the viewport width is greater than 768 px, THE Dashboard Quick Action links SHALL retain all existing `href` attributes and interactive styling without modification.

---

### Requirement 4 — "View All" and Panel Navigation Links Disabled on Mobile

**User Story:** As a mobile user, I want in-panel navigation links on the Dashboard (such as "View All →" for Recent Sales and "View →" for Appointments) to be non-functional, so that I cannot navigate to restricted routes from the Dashboard.

#### Acceptance Criteria

1. WHILE the viewport width is ≤ 768 px, THE Dashboard view SHALL hide panel navigation links (elements with class `panel-link`) using a CSS rule `display: none` inside a `@media(max-width: 768px)` block.
2. WHILE the viewport width is greater than 768 px, THE panel navigation links SHALL retain their existing appearance and behavior without modification.

---

### Requirement 5 — Unmodified Desktop Experience

**User Story:** As a desktop user, I want the application to behave exactly as it does today, so that the mobile restriction feature has zero impact on my workflow.

#### Acceptance Criteria

1. WHEN a Desktop client accesses any route, THE application SHALL serve the full response including sidebar, all navigation links, Quick Actions, and panel links, identical to the behavior prior to this feature.
2. THE MobileRestrictionMiddleware SHALL not alter any HTTP response for Desktop clients.
3. THE CSS changes introduced for mobile restriction SHALL be scoped exclusively to `@media(max-width: 768px)` rules so that styles at viewport widths greater than 768 px are unaffected.
