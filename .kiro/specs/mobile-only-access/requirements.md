# Requirements Document

## Introduction

This feature restricts access to the **Login page** and **Dashboard page** of the Crimpers Salon POS application exclusively to **mobile/small-screen** devices (viewport width ≤ 768 px). On larger screens (tablets and desktops), those two pages must be inaccessible, showing a "not supported" message instead. Additionally, while on a small-screen device, any dashboard navigation links or buttons that would route to other pages must be visually disabled and non-functional, and any attempt by a small-screen user to navigate to any route other than the login or dashboard must be intercepted and redirected back to the dashboard.

All other pages of the application remain desktop-only and are unaffected by this feature.

---

## Glossary

- **Mobile_Guard**: The Laravel middleware responsible for enforcing screen-size-based access rules on the server side.
- **Mobile_Banner**: The full-page "not supported on this device" view rendered by Mobile_Guard when a large-screen user attempts to access a mobile-only page.
- **Dashboard**: The admin index page served at route `admin.index` (GET `/`).
- **Login_Page**: The authentication page served at route `login` (GET `/login`).
- **Mobile_Screen**: A browser viewport with a CSS `window.innerWidth` ≤ 768 px, detected client-side, OR a `User-Agent` heuristic that indicates a handheld device, detected server-side.
- **Large_Screen**: A browser viewport with a CSS `window.innerWidth` > 768 px or any non-handheld User-Agent.
- **Navigation_Control**: Any `<a>` element, `<button>` element, or other interactive element on the Dashboard that links to a route other than `admin.index` or `login`.
- **Small_Screen_Redirect_Guard**: The middleware or JavaScript logic that intercepts navigation to non-allowed routes on mobile screens and redirects back to the Dashboard.

---

## Requirements

### Requirement 1: Large-Screen Blocking for Mobile-Only Pages

**User Story:** As a product owner, I want desktop and tablet users to be prevented from accessing the Login and Dashboard pages, so that those pages are exclusively reserved for mobile device usage.

#### Acceptance Criteria

1. WHEN a Large_Screen user requests the Login_Page (GET `/login`), THE Mobile_Guard SHALL render the Mobile_Banner instead of the login form.
2. WHEN a Large_Screen user requests the Dashboard (GET `/`), THE Mobile_Guard SHALL render the Mobile_Banner instead of the dashboard content.
3. THE Mobile_Banner SHALL display a clear, human-readable message explaining that the requested page is only accessible on mobile devices.
4. THE Mobile_Banner SHALL display a CSS breakpoint-consistent visual that is legible on screen widths greater than 768 px.
5. WHEN the Mobile_Guard renders the Mobile_Banner, THE Mobile_Guard SHALL return HTTP status code 200 (not a redirect or error code) so bookmarked URLs still resolve without errors.

---

### Requirement 2: Small-Screen Accessibility for Login and Dashboard

**User Story:** As a salon staff member using a mobile phone, I want to be able to access the Login page and Dashboard normally, so that I can authenticate and view the dashboard on my device.

#### Acceptance Criteria

1. WHEN a Mobile_Screen user requests the Login_Page, THE Mobile_Guard SHALL allow the request to proceed normally and render the login form.
2. WHEN a Mobile_Screen user requests the Dashboard, THE Mobile_Guard SHALL allow the request to proceed normally and render the dashboard content.
3. WHILE a Mobile_Screen user is authenticated, THE Mobile_Guard SHALL not block access to the Dashboard.

---

### Requirement 3: Disabled Navigation Controls on the Dashboard for Mobile Users

**User Story:** As a product owner, I want all navigation links and buttons on the dashboard to be non-functional on mobile screens, so that mobile users cannot accidentally navigate away from the dashboard to pages that are not mobile-friendly.

#### Acceptance Criteria

1. WHILE the Dashboard is rendered on a Mobile_Screen, THE Dashboard SHALL render every Navigation_Control with a `disabled` attribute or equivalent `pointer-events: none` styling that prevents click and tap events.
2. WHILE the Dashboard is rendered on a Mobile_Screen, THE Dashboard SHALL render every Navigation_Control with a visual indicator (e.g., reduced opacity or a "not available" cursor) that communicates to the user that navigation is not available.
3. WHILE the Dashboard is rendered on a Mobile_Screen, THE Dashboard SHALL NOT include any JavaScript `click` handlers that would navigate the browser to another route when a Navigation_Control is interacted with.
4. WHILE the Dashboard is rendered on a Mobile_Screen, any `<a>` Navigation_Control SHALL have its `href` attribute replaced or overridden so that activating it does not change the browser location.

---

### Requirement 4: Server-Side Redirect for Mobile Users on Non-Allowed Routes

**User Story:** As a product owner, I want mobile users who somehow reach any route other than the Login page or Dashboard to be automatically redirected back to the Dashboard, so that mobile users are confined to the intended mobile experience.

#### Acceptance Criteria

1. WHEN a Mobile_Screen user submits a GET request to any authenticated route other than `admin.index` (GET `/`) and `login` (GET `/login`), THE Small_Screen_Redirect_Guard SHALL redirect the user to the Dashboard (`admin.index`).
2. WHEN a Mobile_Screen user submits a non-GET request (POST, PUT, DELETE, PATCH) to any route other than `login` (POST `/login`) and `logout` (POST `/logout`), THE Small_Screen_Redirect_Guard SHALL allow the request to proceed so that form submissions (e.g., login form POST) are not broken.
3. IF a Mobile_Screen user is unauthenticated and requests a non-login route, THEN THE Small_Screen_Redirect_Guard SHALL redirect the user to the Login_Page (`login`), not the Dashboard, so that the standard authentication flow is preserved.
4. THE Small_Screen_Redirect_Guard SHALL use the `User-Agent` HTTP request header to determine whether a request originates from a Mobile_Screen.
5. WHEN the Small_Screen_Redirect_Guard detects a mobile user-agent, THE Small_Screen_Redirect_Guard SHALL apply the redirect rules defined in AC 1–3 of this requirement.

---

### Requirement 5: No Change to Desktop Behavior for Other Pages

**User Story:** As a desktop user, I want all pages other than the Login page and Dashboard to remain fully accessible and functional on my device, so that my existing workflow is unaffected.

#### Acceptance Criteria

1. WHILE a Large_Screen user accesses any route other than Login_Page and Dashboard, THE application SHALL serve the requested page without any additional restriction introduced by this feature.
2. THE Mobile_Guard SHALL only intercept requests for the Login_Page route and the Dashboard route; it SHALL NOT be applied to any other route in the application.
3. THE Small_Screen_Redirect_Guard SHALL NOT alter the behavior of any route for Large_Screen users.

---

### Requirement 6: Client-Side Screen-Size Detection Consistency

**User Story:** As a developer, I want the client-side screen-size detection to be consistent with the server-side detection, so that the user experience is not broken by mismatches between the two.

#### Acceptance Criteria

1. THE Dashboard SHALL use JavaScript `window.innerWidth` or `matchMedia` to evaluate whether the current viewport qualifies as a Mobile_Screen at page load.
2. WHEN the viewport width changes (e.g., browser window resized) on the Dashboard, THE Dashboard SHALL re-evaluate and update the disabled state of all Navigation_Controls to reflect the current screen size.
3. THE Login_Page SHALL use CSS media queries to hide or visually disable any elements that would be inappropriate on a Large_Screen if the Mobile_Banner is not served at the server level.
