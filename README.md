# AuthMe Plugin

AuthMe is a comprehensive, modern, strictly decoupled WordPress plugin designed to handle user authentication, verification (via OTP), and Host request management.

This plugin focuses entirely on providing a sleek, popup-based frontend experience and a robust backend architecture, without conflicting with any existing themes or WordPress logic.

## 📁 Codebase Architecture & File Structure

The entire codebase has been organized using strict MVC patterns, separating Logic (includes/classes), Views (templates), and Assets (CSS/JS).

### 1. Root Directory

- **`authme.php`**: The main entry point of the plugin.
  - Starts up the plugin and handles versioning.
  - Loads all PHP classes.
  - Registers the `traveller` and `host` roles.
  - Hooks into WordPress to inject the frontend modals (`wp_footer`).
  - Registers the custom virtual rewrite route (`/authme`) to auto-open the popup on the home page.
  - Registers global AJAX routes for all frontend user actions.
- **`composer.json` & `vendor/`**: Manages backend dependencies. This plugin explicitly requires `giggsey/libphonenumber-for-php` to validate international mobile numbers reliably.

---

### 2. `/includes/` (Core Backend Logic)

This directory contains the PHP classes that actually power the functionality.

- **`assets-loader.php`**: Acts as the _Single Source of Truth_ for file mappings. Every CSS, JS, or template file is defined here with its direct server path and public URL. This prevents broken URLs and intelligently manages browser cache-busting using `filemtime()`.
- **`db-schema.php`**: Contains the raw SQL table structure. Defines the `authme_otp_storage` (for keeping temporary OTPs) and `host_request` (for storing user submissions for hosting).
- **`class-authme-db.php`**: The DB Controller. Reads the schema, verifies if tables exist, and creates/updates them if the WordPress environment is missing them. Powered by dbDelta.
- **`class-authme-auth.php`**: The main Authentication Controller. It handles the AJAX endpoints for registering a new user, logging them in, resetting passwords, and validating if a username/email already exists.
- **`class-authme-otp.php`**: Manages all logic surrounding One-Time Passwords. It generates secure 6-digit codes, hashes them, stores them in the DB, and compares them when a user submits an OTP.
- **`class-authme-email.php`**: Manages all outgoing emails. Triggers OTP emails using customized templates and triggers status updates for Host requests (approved/rejected).
- **`class-authme-host-request.php`**: Handles the "Become a Host" multi-step flow logic. It securely intercepts JPEG image uploads, forces them into the WordPress Media Library as private attachments, and manages the AJAX logic for completing host applications.
- **`assets/global.css`**: The core design system tokens. Defines global CSS variables (colors, borders, fonts) used across every single frontend and backend stylesheet.

---

### 3. `/frontend/` (User Facing UI & Client Logic)

This directory controls everything a normal visitor sees.

- **`templates/` (Views)**:
  - **`overlay.php`**: The primary backdrop modal injection container. It holds standard screens like Login, Registration, and Password Reset.
  - **`login.php` | `register.php` | `otp.php` | `forgot-password.php` | `new-password.php`**: The HTML forms rendered inside the overlay.
  - **`toaster.php`**: The global notification element used for displaying success/error popup alerts.
  - **`host-request.php`**: The multi-step modal structure for users applying to become a Host.
  - **`email-*.php`**: Formatted HTML templates sent to the user's inbox on OTP or notification triggers.

- **`assets/js/` (Client Logic)**:
  - **`global.js`**: Contains a universal AJAX wrapper `authmeAjax()`, regex validation helpers, and DOM state classes used by every script.
  - **`overlay.js`**: Handles the opening/closing animations of the main popup and screen transitions.
  - **`login.js` | `register.js` | `otp.js` | `forgot-password.js` | `new-password.js`**: Specific DOM binders and AJAX execution for each respective step.
  - **`country-phone-regex.js`**: Hardcoded front-end regex mapping to validate mobile numbers quickly before sending to the server.
  - **`host-request.js`**: Manages the complex state of the Host Registration flow. Tracks the current step, handles direct file uploads using `XMLHttpRequest` with progress bars, and sends the final JSON state to the backend.

- **`assets/css/` (Styling)**:
  - Contains precisely scoped CSS for every template. E.g., `register.css` strictly maps its styles to the classes inside `register.php`. Ensures zero bleed/conflicts globally.

---

### 4. `/admin/` (Administrator Dashboard & Management)

Controls the interfaces seen by Administrators in the WP Backend (accessible via the `AuthMe` menu).

- **`class-authme-admin.php`**: Handles wp-admin menu definitions. Provides the secure backend-only AJAX routes for approving/rejecting Hosts and querying the custom database tables.
- **`templates/` (Admin Views)**:
  - **`dashboard.php`**: Welcome screen and analytics.
  - **`database.php`**: Health check page for the plugin's custom tables.
  - **`host-requests.php`**: Data table UI listing all incoming and processed host applications.
  - **`view-form.php`**: Detailed view modal showing exactly what the user submitted, including document previews.
  - **`admin-toaster.php`**: A separate toaster for admin panel notifications.
- **`assets/js/` & `assets/css/`**:
  - Dedicated scripts and styling for the internal WordPress admin interface views.

## ⚙️ Key Technical Features & Workflows

### 1. The Global Entry Point (Overlay Injection)

If a user goes to `yoursite.com/authme`, the plugin redirects them to `yoursite.com/?authme_open=1`. The `wp_footer` hook detects this trigger and auto-opens the JS overlay automatically.

### 2. Multi-Step Host Registry with Media Upload

The "Become a Host" popup avoids heavy Base64 storage. It leverages temporary asynchronous file uploads directly into WordPress's core media uploads function (`wp_handle_upload`) along with real-time UI progress bars. The DB only receives the final URL locations.

### 3. Role Checking

The platform utilizes standard WordPress roles. New signups are immediately categorized as `traveller`. If a user is successfully approved as a Host via the backend dashboard, they are automatically upgraded to `host`. The platform proactively hides Host-only features from Travellers and Vice Versa.

### 4. Zero Conflicting Styles

This plugin relies on **100% custom CSS** (no Tailwind, no Bootstrap). A single `global.css` specifies core HSL tokens. Every other CSS file uses these tokens natively, meaning color/branding changes only happen in a single place.

### 5. International Number Formatting

To prevent invalid spam entries, we combine Frontend Regex Checks (for fast UI response) with Backend checking (`libphonenumber-for-php`) to guarantee the database holds clean, fully validated formatted numbers.

---

_AuthMe Plugin was designed for maximum scalability, caching optimization, and clean WordPress coding standards._
