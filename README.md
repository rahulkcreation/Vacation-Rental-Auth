# AuthMe WordPress Plugin

**Version:** 1.8.0  
**Author:** Art-Tech Fuzion  
**Requires:** WordPress 5.0+, PHP 7.4+  
**Plugin URI:** https://arttechfuzion.com  
**Text Domain:** authme

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
   - [Installing Composer](#installing-composer)
     - [Windows](#installing-composer-on-windows)
     - [macOS](#installing-composer-on-macos)
   - [Installing PHP Dependencies](#installing-php-dependencies-vendor-files)
5. [How the Plugin Works](#how-the-plugin-works)
   - [System Architecture](#system-architecture)
   - [User Registration Flow](#user-registration-flow)
   - [User Login Flow](#user-login-flow)
   - [Password Reset Flow](#password-reset-flow)
   - [Become a Host Flow](#become-a-host-flow)
6. [File Structure](#file-structure)
7. [Database](#database)
8. [Admin Panel](#admin-panel)
9. [Security Features](#security-features)
10. [AJAX Actions Reference](#ajax-actions-reference)
11. [Hooks & Filters](#hooks--filters)
12. [Troubleshooting](#troubleshooting)
13. [Support](#support)
14. [License](#license)

---

## Overview

AuthMe is a comprehensive WordPress authentication plugin that provides a secure, modern authentication system with OTP (One-Time Password) verification for user registration and login flows.

The plugin replaces the default WordPress login/registration with a beautiful popup overlay that includes:
- Real-time username and email availability checking
- Mobile number validation with international support
- OTP-based email verification for registration
- Password strength validation
- Password reset with OTP verification
- Custom "traveller" user role for new users
- "Host" user role for property owners
- Host Request Application System (Become a Host modal)

---

## Features

| Feature | Description |
|---------|-------------|
| **Popup Authentication Overlay** | Beautiful modal popup for all auth actions |
| **OTP Verification** | 6-digit one-time password sent via email |
| **Real-time Validation** | Instant feedback on username/email availability |
| **Password Strength Meter** | Visual indicator for password security |
| **International Mobile Support** | Phone number validation using libphonenumber |
| **Custom User Roles** | 'traveller' for users, 'host' for property owners |
| **Admin Dashboard** | Manage plugin settings and database |
| **Email Notifications** | Beautiful HTML emails for OTP and password changes |
| **Scheduled Cleanup** | Automatic cleanup of expired OTPs via cron |
| **Host Request System** | Multi-step modal for host applications with document upload |

---

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **PHP Extensions:** json, mbstring (for libphonenumber)
- **Composer:** For installing PHP dependencies

---

## Installation

### Step 1: Install Composer

Composer is a dependency manager for PHP. AuthMe uses Composer to install the `libphonenumber-for-php` library for validating international phone numbers.

#### Installing Composer on Windows

**Method 1: Using Composer Installer (Recommended)**

1. Download the Composer installer from [getcomposer.org](https://getcomposer.org/Composer-Setup.exe)
2. Run the installer (Composer-Setup.exe)
3. Follow the installation wizard:
   - Select your PHP executable path (usually found in your local server setup like XAMPP, WAMP, or MAMP)
   - Enable developer mode if you want (optional)
   - Complete the installation
4. Open Command Prompt and verify installation:
   ```
   composer --version
   ```

**Method 2: Using Manual Installation**

1. Make sure PHP is in your system PATH
2. Download Composer for Windows from [getcomposer.org/download](https://getcomposer.org/download/)
3. Run the following commands in Command Prompt:
   ```
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   move composer.phar C:\bin\composer.phar
   echo @php "%~dp0composer.phar" %*>C:\bin\composer.bat
   ```
4. Add `C:\bin` to your system PATH
5. Verify: `composer --version`

---

#### Installing Composer on macOS

**Method 1: Using Homebrew (Recommended)**

1. Open Terminal
2. If Homebrew is not installed, install it:
   ```bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```
3. Install Composer via Homebrew:
   ```bash
   brew install composer
   ```
4. Verify installation:
   ```bash
   composer --version
   ```

**Method 2: Using PHP Installer Script**

1. Open Terminal
2. Download the Composer installer:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   ```
3. Move Composer to a global location:
   ```bash
   mv composer.phar /usr/local/bin/composer
   ```
4. Make it executable:
   ```bash
   chmod +x /usr/local/bin/composer
   ```
5. Verify installation:
   ```bash
   composer --version
   ```

**Method 3: Using Mac's Built-in PHP**

1. Open Terminal
2. Download Composer:
   ```bash
   curl -sS https://getcomposer.org/installer -o composer-setup.php
   ```
3. Run the installer:
   ```bash
   php composer-setup.php --install-dir=/usr/local/bin --filename=composer
   ```
4. Verify:
   ```bash
   composer --version
   ```

---

### Step 2: Install PHP Dependencies

Navigate to the plugin directory and run:

```bash
cd /path/to/your/wordpress/wp-content/plugins/authme-2

composer require giggsey/libphonenumber-for-php
```

**Important Notes:**
- The `vendor/` folder is an external dependency generated by Composer
- Do NOT add the `vendor/` folder to git
- The libphonenumber library is used for phone number validation (India, US, UK, Australia, UAE, Singapore, etc.)

---

### Step 3: Upload to WordPress

**Include these files/folders:**
- `authme.php` - Main plugin file
- `composer.json` - For reference
- `index.php` - Security files (created in all folders)
- `includes/` folder - PHP classes
- `frontend/` folder - Templates and assets
- `admin/` folder - Admin panel
- `vendor/` folder - Generated by Composer (REQUIRED)

**Do NOT modify:**
- Files inside `vendor/` - External dependencies

---

## How the Plugin Works

### System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    WordPress Core                        │
└─────────────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│                    authme.php                            │
│              (Main Plugin Entry Point)                   │
└─────────────────────────────────────────────────────────┘
                             │
         ┌───────────────────┼───────────────────┐
         ▼                   ▼                   ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│  AuthMe_Auth  │  │  AuthMe_OTP   │  │ AuthMe_Email  │
│  (Login/Reg)  │  │  (6-digit)    │  │  (Sending)    │
└───────────────┘  └───────────────┘  └───────────────┘
         │                   │                   │
         └───────────────────┼───────────────────┘
                             ▼
┌─────────────────────────────────────────────────────────┐
│                   AuthMe_DB                              │
│              (Database Operations)                       │
└─────────────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│              wp_authme_otp_storage                       │
│           (Custom Database Table)                        │
└─────────────────────────────────────────────────────────┘
```

### User Registration Flow

```
1. User clicks "Register" → Opens overlay on "register" screen
   │
   ▼
2. User fills form fields:
   - Username (real-time availability check)
   - Email (real-time availability check)
   - Mobile (international format validation via libphonenumber)
   - Password (strength meter shown)
   │
   ▼
3. User clicks "Send OTP"
   │
   ├─► AJAX: authme_send_otp
   │    │
   │    ├─► Generate 6-digit random OTP
   │    ├─► Store in wp_authme_otp_storage (purpose: 'registration')
   │    ├─► Store user data (username, email, password hash) in user_data column
   │    ├─► Send OTP via email
   │    └─► Return success/error
   │
   ▼
4. OTP screen appears:
   - 60-second countdown timer
   - 6 input fields for OTP digits
   - Auto-focus, auto-tab between fields
   │
   ▼
5. User enters OTP and clicks "Verify & Proceed"
   │
   ├─► AJAX: authme_verify_otp
   │    │
   │    ├─► Check OTP exists and not expired (60 seconds)
   │    ├─► Check OTP matches
   │    ├─► Mark as verified in database
   │    └─► Return success/error
   │
   ▼
6. If OTP verified:
   │
   ├─► AJAX: authme_register_user
   │    │
   │    ├─► Retrieve stored user_data
   │    ├─► Check OTP is verified
   │    ├─► Create WordPress user with 'traveller' role
   │    ├─► Set user metadata (mobile, email, verified status)
   │    ├─► Auto-login user
   │    ├─► Clean up OTP record
   │    └─► Return success
   │
   ▼
7. Success! Overlay closes, page reloads
   └─► User is now logged in as "traveller"
```

### User Login Flow

```
1. User clicks "Login" → Opens overlay on "login" screen
   │
   ▼
2. User enters email/username
   │
   ├─► AJAX: authme_check_user_exists
   │    │
   │    ├─► Look up user by email or username
   │    └─► Return user exists status (enables password field)
   │
   ▼
3. User enters password
   │
   ▼
4. User clicks "Login"
   │
   ├─► AJAX: authme_login_user
   │    │
   │    ├─► Authenticate credentials via wp_signon
   │    ├─► Set auth cookie
   │    ├─► Update last login metadata
   │    └─► Return success/error
   │
   ▼
5. Success! Overlay closes, page reloads
   └─► User is now logged in
```

### Password Reset Flow

```
1. User clicks "Forgot Password?" → Opens "forgot-password" screen
   │
   ▼
2. User enters email/username
   │
   ├─► AJAX: authme_forgot_check_user
   │    │
   │    ├─► Look up WordPress user
   │    └─► Return user found status
   │
   ▼
3. User clicks "Send Reset OTP"
   │
   ├─► AJAX: authme_send_otp
   │    │
   │    ├─► Generate 6-digit OTP
   │    ├─► Store (purpose: 'password_reset')
   │    ├─► Send via email
   │    └─► Return success/error
   │
   ▼
4. OTP screen appears (60-second countdown)
   │
   ▼
5. User enters OTP and clicks "Verify"
   │
   ├─► AJAX: authme_verify_otp
   │    │
   │    ├─► Verify OTP
   │    └─► Return success (enables new password form)
   │
   ▼
6. New password screen:
   - New password field (with strength meter)
   - Confirm password field
   │
   ▼
7. User enters new password and clicks "Reset Password"
   │
   ├─► AJAX: authme_reset_password
   │    │
   │    ├─► Verify OTP is still valid
   │    ├─► Update user password via wp_set_password
   │    ├─► Send "password changed" notification email
   │    ├─► Clean up OTP record
   │    └─► Return success
   │
   ▼
8. Success! User can now login with new password
```

### Become a Host Flow

```
1. User visits /?become-host URL
   │
   ▼
2. Step 1: Personal Information
   - Username (real-time availability check)
   - Full Name (min 3 characters)
   - Email (real-time availability check)
   - Mobile with country code (validation)
   │
   ▼
3. Step 2: Document Upload
   - Aadhar Card Front (JPEG, max 1MB)
   - Aadhar Card Back (JPEG, max 1MB)
   - PAN Card Front (JPEG, max 1MB)
   │
   ▼
4. Step 3: OTP Verification
   - 6-digit OTP sent to email
   - Verify & Submit application
   │
   ▼
5. Step 4: Success Message
   - Application submitted
   - Auto-close after 15 seconds
```

**Admin Flow:**
- Admin receives notification in WordPress admin panel
- Admin can View, Approve, or Reject the application
- If Approved: New user created with 'host' role, credentials sent via email
- If Rejected: Email notification sent to applicant

---

## File Structure

```
AuthMe/
├── authme.php                 # MAIN PLUGIN ENTRY POINT
│                              # Plugin initialization, hooks, AJAX registration
├── index.php                  # Security: Prevents directory browsing
├── composer.json              # PHP dependencies definition
│
├── includes/                  # PHP CLASS FILES
│   ├── index.php              # Security file
│   ├── assets-loader.php      # CENTRALIZED ASSET MANAGEMENT
│   │                           # All CSS/JS file paths defined here (SINGLE SOURCE OF TRUTH)
│   ├── class-authme-auth.php   # AUTHENTICATION HANDLER
│   │                           # AJAX: check_username, check_email, check_mobile,
│   │                           #       login_user, register_user, forgot_check_user, reset_password
│   ├── class-authme-db.php     # DATABASE MANAGER
│   │                           # Table creation, status check
│   ├── class-authme-email.php  # EMAIL HANDLER
│   │                           # send_otp_email, send_password_changed_email,
│   │                           # send_host_approved_email, send_host_rejected_email
│   ├── class-authme-otp.php    # OTP MANAGER
│   │                           # ajax_send_otp, ajax_verify_otp, cleanup_expired_otps
│   ├── class-authme-host-request.php  # HOST REQUEST HANDLER
│   │                           # ajax_upload_host_document, ajax_check_host_*,
│   │                           # ajax_submit_host_request
│   ├── db-schema.php          # DATABASE SCHEMA REGISTRY
│   │                           # Table definitions, column schemas (SINGLE SOURCE OF TRUTH)
│   └── assets/
│       └── global.css         # Shared CSS variables and global styles
│
├── frontend/                  # FRONTEND TEMPLATES & ASSETS
│   ├── index.php             # Security file
│   ├── assets/
│   │   ├── index.php         # Security file
│   │   ├── css/              # FRONTEND STYLESHEETS
│   │   │   ├── index.php     # Security file
│   │   │   ├── overlay.css   # Popup container styles
│   │   │   ├── login.css     # Login form styles
│   │   │   ├── register.css  # Registration form styles
│   │   │   ├── otp.css       # OTP input styles
│   │   │   ├── forgot-password.css  # Forgot password styles
│   │   │   ├── new-password.css     # New password form styles
│   │   │   ├── host-request.css     # Host modal styles
│   │   │   └── toaster.css   # Toast notification styles
│   │   │
│   │   └── js/               # FRONTEND JAVASCRIPT
│   │       ├── index.php     # Security file
│   │       ├── global.js     # Shared utilities (ajax, validation, password strength)
│   │       ├── overlay.js    # Popup open/close, screen switching
│   │       ├── login.js      # Login form logic
│   │       ├── register.js   # Registration with validation
│   │       ├── otp.js        # OTP input handling, timer
│   │       ├── forgot-password.js   # Password reset flow
│   │       ├── new-password.js      # New password form
│   │       ├── host-request.js      # Multi-step host application
│   │       ├── toaster.js    # Toast notifications
│   │       └── country-phone-regex.js  # Country phone patterns data
│   │
│   └── templates/            # FRONTEND HTML TEMPLATES
│       ├── index.php         # Security file
│       ├── overlay.php       # Main popup container
│       ├── login.php         # Login form
│       ├── register.php      # Registration form
│       ├── otp.php           # OTP verification screen
│       ├── forgot-password.php    # Password reset request
│       ├── new-password.php       # New password form
│       ├── host-request.php       # Become a Host multi-step modal
│       ├── toaster.php       # Toast notification container
│       ├── email-otp.php     # OTP email HTML template
│       ├── email-msg.php     # Message-only email template
│       └── email-details.php    # Email with credentials template
│
├── admin/                    # WORDPRESS ADMIN PANEL
│   ├── index.php             # Security file
│   ├── class-authme-admin.php # ADMIN MENU & AJAX HANDLERS
│   │                           # Menu registration, enqueue admin assets
│   │                           # AJAX: check_db_status, create_tables
│   │                           # AJAX: get_host_requests, get_single_host, process_host
│   ├── assets/
│   │   ├── index.php         # Security file
│   │   ├── css/              # ADMIN STYLESHEETS
│   │   │   ├── index.php     # Security file
│   │   │   ├── admin.css     # General admin styles
│   │   │   ├── admin-toaster.css  # Admin toaster styles
│   │   │   ├── dashboard.css # Dashboard page styles
│   │   │   └── host-requests.css  # Host requests page styles
│   │   │
│   │   └── js/               # ADMIN JAVASCRIPT
│   │       ├── index.php     # Security file
│   │       ├── admin.js      # General admin JS
│   │       ├── admin-toaster.js  # Admin toaster JS
│   │       ├── dashboard.js  # Dashboard page JS
│   │       ├── database.js   # Database management JS
│   │       └── host-requests.js  # Host requests management JS
│   │
│   └── templates/            # ADMIN TEMPLATE PAGES
│       ├── index.php         # Security file
│       ├── dashboard.php     # Main admin dashboard
│       ├── database.php      # Database management page
│       ├── host-requests.php # Host applications list
│       └── view-form.php     # View host request details
│
└── vendor/                   # COMPOSER DEPENDENCIES (DO NOT MODIFY)
    ├── autoload.php         # Composer autoloader
    └── giggsey/
        └── libphonenumber-for-php/  # Google phone number library
```

---

## Database

### Custom Tables

The plugin creates two custom database tables:

#### Table 1: `wp_authme_otp_storage` (prefix may vary)

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| email | VARCHAR(100) | Recipient email address |
| otp_code | VARCHAR(6) | 6-digit OTP code |
| purpose | VARCHAR(20) | Purpose: 'registration', 'login', 'password_reset', 'host_request' |
| created_at | TIMESTAMP | When OTP was created |
| expires_at | TIMESTAMP | When OTP expires (created_at + 60 seconds) |
| is_verified | TINYINT(1) | 0 = pending, 1 = verified |
| user_data | TEXT | JSON data for registration (username, email, password hash) |

#### Table 2: `wp_host_request` (prefix may vary)

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| user_data | LONGTEXT | JSON blob with host application data |
| status | VARCHAR(50) | Status: 'pending', 'approved', 'rejected' |
| date | DATETIME | When application was submitted |

### Created on Plugin Activation

The tables are created automatically when the plugin is activated via `dbDelta()`.
Go to **WordPress Admin > AuthMe > Database** to create/update tables manually if needed.

---

## Admin Panel

Access via WordPress admin menu: **AuthMe**

### Dashboard
- Plugin information and version
- Usage instructions (URLs, trigger links, JavaScript functions)
- Quick links to other admin pages
- Security status

### Database Management
- View table status
- View table columns
- Create/update tables manually
- AJAX-powered status checks

### Host Requests
- View all host applications (pending/approved/rejected)
- Search functionality
- Pagination
- View application details (name, email, phone, documents)
- Approve/Reject actions with email notifications

---

## Security Features

1. **OTP Verification** - Required for registration and password reset
2. **Nonce Validation** - All AJAX requests use WordPress security nonces
3. **Password Hashing** - Uses WordPress password hashing (`wp_hash_password`)
4. **Admin Protection** - Administrators cannot use the popup for login
5. **Input Sanitization** - All user inputs are sanitized
6. **OTP Expiry** - 60-second validity with automatic cleanup
7. **Automatic Cleanup** - Cron job removes expired/verified OTPs twice daily
8. **File Upload Security** - Only JPEG images, 1MB limit, processed via wp_handle_upload

---

## AJAX Actions Reference

### Authentication AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_check_username` | `AuthMe_Auth::ajax_check_username` | Check if username is available |
| `authme_check_email` | `AuthMe_Auth::ajax_check_email` | Check if email is available |
| `authme_check_mobile` | `AuthMe_Auth::ajax_check_mobile` | Check if mobile number is available |
| `authme_check_user_exists` | `AuthMe_Auth::ajax_check_user_exists` | Check if user exists for login |
| `authme_login_user` | `AuthMe_Auth::ajax_login_user` | Authenticate and login user |
| `authme_register_user` | `AuthMe_Auth::ajax_register_user` | Create new WordPress user (after OTP) |
| `authme_forgot_check_user` | `AuthMe_Auth::ajax_forgot_check_user` | Check user for password reset |
| `authme_reset_password` | `AuthMe_Auth::ajax_reset_password` | Reset user password (after OTP) |

### OTP AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_send_otp` | `AuthMe_OTP::ajax_send_otp` | Generate and send OTP |
| `authme_verify_otp` | `AuthMe_OTP::ajax_verify_otp` | Verify OTP code |

### Host Request AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_check_host_username` | `AuthMe_Host_Request::ajax_check_host_username` | Check username for host |
| `authme_check_host_email` | `AuthMe_Host_Request::ajax_check_host_email` | Check email for host |
| `authme_check_host_mobile` | `AuthMe_Host_Request::ajax_check_host_mobile` | Check mobile for host |
| `authme_upload_host_document` | `AuthMe_Host_Request::ajax_upload_host_document` | Upload host document |
| `authme_submit_host_request` | `AuthMe_Host_Request::ajax_submit_host_request` | Submit host application |

---

## Hooks & Filters

### Actions

| Hook | Description |
|------|-------------|
| `authme_otp_cleanup` | Scheduled cron action for cleaning expired OTPs |
| `wp_enqueue_scripts` | Enqueue frontend assets |
| `wp_footer` | Inject overlay HTML |
| `admin_menu` | Register admin menus |
| `admin_enqueue_scripts` | Enqueue admin assets |
| `init` | Register rewrite rules |
| `template_redirect` | Handle virtual page |

### Filters

| Hook | Description |
|------|-------------|
| `query_vars` | Register custom query variables |
| `plugin_action_links_{plugin_basename}` | Add settings link to plugins page |

### Constants

| Constant | Description |
|----------|-------------|
| `AUTHME_VERSION` | Plugin version |
| `AUTHME_PLUGIN_DIR` | Server path to plugin directory |
| `AUTHME_PLUGIN_URL` | URL to plugin directory |
| `AUTHME_PLUGIN_BASENAME` | Plugin basename identifier |

---

## Troubleshooting

### Plugin not working after upload

1. Make sure you ran `composer require giggsey/libphonenumber-for-php` and the `vendor` folder exists
2. Verify the `vendor/giggsey/libphonenumber-for-php` folder was created (this is required for phone number validation)
3. Check PHP version is 7.4 or higher
4. Verify WordPress version is 5.0 or higher
5. Check that the `json` and `mbstring` PHP extensions are enabled

### OTP not being sent

1. Check that WordPress can send emails (use WP Mail SMTP plugin)
2. Verify the email address is correct
3. Check spam/junk folders
4. Ensure the `wp_authme_otp_storage` table exists

### Composer not working

1. Verify Composer is installed: `composer --version`
2. Check PHP is in system PATH
3. If there are memory issues: `php -d memory_limit=512M composer require giggsey/libphonenumber-for-php`
4. Make sure you're running the command from the plugin directory

### Database table not created

1. Go to WordPress Admin > AuthMe > Database
2. Click "Create/Update Table"
3. Check for any database errors in PHP error log

### Overlay not appearing

1. Make sure you're logged out (popup only shows for non-logged-in users)
2. Check browser console for JavaScript errors
3. Verify assets are being loaded (check network tab)
4. Try visiting `/authme` URL directly

### Host Request Issues

1. Make sure all required documents are uploaded (Aadhar front, Aadhar back, PAN)
2. Only JPEG images are allowed (max 1MB each)
3. After submission, check admin panel for pending requests
4. Admin can approve/reject and system will auto-create user

---

## Support

For issues and feature requests, contact the author at https://arttechfuzion.com

---

## License

This plugin is proprietary software by Art-Tech Fuzion.
