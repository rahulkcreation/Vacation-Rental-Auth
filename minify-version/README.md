# AuthMe - Vacation Rental Authentication Plugin

**Version:** 2.1.9  
**Author:** Art-Tech Fuzion  
**Requires:** WordPress 5.0+, PHP 7.4+  
**Plugin URI:** https://arttechfuzion.com  
**Text Domain:** authme  

---

## Table of Contents

1. [Introduction](#introduction)
2. [Who Should Use AuthMe?](#who-should-use-authme)
3. [Key Features](#key-features)
4. [System Requirements](#system-requirements)
5. [Installation Guide](#installation-guide)
6. [User Guide](#user-guide)
   - [Registration Flow](#registration-flow)
   - [Login Flow](#login-flow)
   - [Password Reset Flow](#password-reset-flow)
   - [Become a Host Flow](#become-a-host-flow)
   - [Google Authentication](#google-authentication)
7. [Developer Guide](#developer-guide)
   - [Architecture Overview](#architecture-overview)
   - [File Structure](#file-structure)
   - [Database Schema](#database-schema)
   - [AJAX Actions Reference](#ajax-actions-reference)
   - [Hooks & Filters](#hooks--filters)
   - [Constants](#constants)
8. [Admin Dashboard](#admin-dashboard)
9. [Security Features](#security-features)
10. [Troubleshooting](#troubleshooting)
11. [Support](#support)
12. [License](#license)

---

## Introduction

AuthMe is a comprehensive WordPress authentication plugin specifically designed for vacation rental websites. It provides a secure, modern, and user-friendly authentication system with OTP (One-Time Password) email verification for all user interactions.

The plugin replaces the default WordPress login/registration with a beautiful, responsive popup overlay that works seamlessly across all devices. AuthMe introduces two custom user roles—"Traveller" for guests and "Host" for property owners—along with a complete host application management system.

---

## Who Should Use AuthMe?

### Website Owners Who Should Use AuthMe

AuthMe is ideal for:

- **Vacation Rental Platforms** - Websites like Airbnb, Vrbo, or Booking clones where users need to book properties and property owners need to list rentals
- **Property Listing Websites** - Real estate portals with host/guest relationships
- **Travel & Tourism Websites** - Platforms connecting travelers with local hosts
- **Multi-vendor Marketplace** - Any marketplace requiring distinct user roles (buyers/sellers or guests/hosts)
- **Hospitality Service Providers** - Hotels, resorts, and homestays with online booking

### When You Need AuthMe

Use AuthMe if your website requires:

| Need | How AuthMe Helps |
|------|------------------|
| **Secure User Registration** | OTP-based email verification prevents fake/spam accounts |
| **Dual User Roles** | Separate "Traveller" and "Host" roles with different capabilities |
| **Host Application System** | Multi-step application process with document verification |
| **Modern UI Experience** | Beautiful popup overlay instead of redirecting to WordPress login |
| **International Users** | Phone number validation for 150+ countries via am-phone-core.js |
| **Admin Control** | Dashboard to manage host applications, view users, monitor database |

---

## Key Features

| Feature | Description |
|---------|-------------|
| **Popup Authentication Overlay** | Beautiful modal popup for all authentication actions (login, register, password reset) |
| **OTP Email Verification** | 6-digit one-time password sent via email for registration and password reset |
| **Real-time Validation** | Instant feedback on username and email availability as users type |
| **Password Strength Meter** | Visual indicator showing password security level |
| **International Phone Validation** | Phone number validation via am-phone-core.js supporting 150+ countries |
| **Custom User Roles** | "Traveller" for regular users, "Host" for property owners |
| **Host Request System** | Multi-step application modal with document upload (Aadhar, PAN) |
| **Google Authentication** | One-click login via Google OAuth integration |
| **Admin Dashboard** | WordPress admin panel to manage plugin settings, users, and host requests |
| **Email Notifications** | Beautiful HTML email templates for OTP, password changes, host approval/rejection |
| **Automatic OTP Cleanup** | Scheduled cron job removes expired/verified OTPs twice daily |
| **Security Protections** | Nonce validation, password hashing, input sanitization, admin access restrictions |

---

## System Requirements

| Requirement | Minimum Version |
|-------------|-----------------|
| WordPress | 5.0 or higher |
| PHP | 7.4 or higher |
| PHP Extensions | json, mbstring |
| MySQL | 5.0 or higher |
| Browser | Modern browsers (Chrome, Firefox, Safari, Edge) |

---

## Installation Guide

### Method 1: Upload via WordPress Admin

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Click **Upload Plugin** and select the AuthMe zip file
4. Click **Install Now** and then **Activate**

### Method 2: Manual Upload

1. Download the AuthMe plugin folder
2. Upload the `authme` folder to `/wp-content/plugins/` directory
3. Activate the plugin through the **Plugins** menu in WordPress

### Post-Installation

After activation:
- The plugin automatically creates required database tables
- Two custom user roles ("Traveller" and "Host") are registered
- A daily cron job is scheduled for OTP cleanup
- Rewrite rules are flushed for virtual pages

**Important Files to Include:**
- `authme.php` - Main plugin file
- `includes/` - PHP classes and core functionality
- `frontend/` - Templates, styles, and frontend JavaScript
- `backend/` - Admin panel functionality
- `components/` - Shared components (Google Auth, phone validation, toaster notifications)

---

## User Guide

### Registration Flow

```
1. User clicks "Register" button
   │
   ▼
2. Registration form appears with fields:
   - Username (real-time availability check)
   - Email (real-time availability check)
   - Mobile number with country code (international validation)
   - Password (with strength meter)
   │
   ▼
3. User clicks "Send OTP"
   │
   ├─► AJAX sends data to server
   │    ├─► 6-digit OTP generated
   │    ├─► Stored in database with user data
   │    ├─► OTP sent via email
   │    └─► Success response returned
   │
   ▼
4. OTP verification screen:
   - 60-second countdown timer
   - 6 individual input fields
   - Auto-focus and auto-tab between fields
   │
   ▼
5. User enters OTP and clicks "Verify & Proceed"
   │
   ├─► AJAX verifies OTP
   │    ├─► Checks OTP validity (60 seconds)
   │    ├─► Validates OTP matches
   │    └─► Marks as verified in database
   │
   ▼
6. If OTP verified:
   │
   ├─► AJAX creates WordPress user
   │    ├─► Retrieves stored user data
   │    ├─► Creates user with "traveller" role
   │    ├─► Stores mobile number in user meta
   │    ├─► Auto-logs in the user
   │    └─► Cleans up OTP record
   │
   ▼
7. Success! Popup closes, page refreshes
   └─► User is now logged in as "Traveller"
```

### Login Flow

```
1. User clicks "Login" button
   │
   ▼
2. Login form appears with fields:
   - Email/Username
   - Password (appears after user lookup)
   │
   ▼
3. User enters email/username
   │
   ├─► AJAX checks if user exists
   │    ├─► Looks up user by email or username
   │    └─► Returns user exists status
   │
   ▼
4. User enters password and clicks "Login"
   │
   ├─► AJAX authenticates user
   │    ├─► Validates credentials via wp_signon
   │    ├─► Sets authentication cookie
   │    ├─► Updates last login metadata
   │    └─► Returns success/error
   │
   ▼
5. Success! Popup closes, page refreshes
   └─► User is now logged in
```

### Password Reset Flow

```
1. User clicks "Forgot Password?" link
   │
   ▼
2. Password reset form appears
   │
   ▼
3. User enters email/username
   │
   ├─► AJAX checks if user exists
   │    └─► Returns user found status
   │
   ▼
4. User clicks "Send Reset OTP"
   │
   ├─► AJAX generates and sends OTP
   │    ├─► 6-digit OTP generated
   │    ├─► Stored with "password_reset" purpose
   │    ├─► OTP sent via email
   │    └─► Success response returned
   │
   ▼
5. OTP verification screen (60-second countdown)
   │
   ▼
6. User enters OTP and clicks "Verify"
   │
   ├─► AJAX verifies OTP
   │    └─► Returns success (enables new password form)
   │
   ▼
7. New password screen:
   - New password field (with strength meter)
   - Confirm password field
   │
   ▼
8. User enters new password and clicks "Reset Password"
   │
   ├─► AJAX processes password reset
   │    ├─► Verifies OTP is still valid
   │    ├─► Updates user password via wp_set_password
   │    ├─► Sends "password changed" notification email
   │    └─► Cleans up OTP record
   │
   ▼
9. Success! User can now login with new password
```

### Become a Host Flow

```
1. User visits /?become-host URL or clicks "Become a Host"
   │
   ▼
2. Step 1: Personal Information
   - Username (real-time availability check)
   - Full Name (minimum 3 characters)
   - Email (real-time availability check)
   - Mobile with country code (validation)
   │
   ▼
3. Step 2: Document Upload
   - Aadhar Card Front (JPEG only, max 1MB)
   - Aadhar Card Back (JPEG only, max 1MB)
   - PAN Card Front (JPEG only, max 1MB)
   │
   ▼
4. Step 3: OTP Verification
   - 6-digit OTP sent to email
   - Verify & Submit application
   │
   ▼
5. Step 4: Success Message
   - Application submitted
   - Modal auto-closes after 15 seconds
```

**Admin Processing:**
- Admin receives notification in WordPress dashboard
- Admin reviews application details and uploaded documents
- Admin can **Approve** or **Reject** the application
- **If Approved:** New user created with "Host" role, credentials sent via email
- **If Rejected:** Email notification sent to applicant with reason

### Google Authentication

AuthMe supports one-click Google login:
- Users can click "Continue with Google" on the login screen
- Requires Google Client ID configuration in the admin panel
- Automatically creates a "Traveller" user account upon first login
- Security alerts sent via email for new Google logins

---

## Developer Guide

### Architecture Overview

AuthMe follows a modular, object-oriented architecture with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────┐
│                    WordPress Core                            │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    authme.php                               │
│              (Main Plugin Entry Point)                       │
└─────────────────────────────────────────────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌───────────────┐    ┌───────────────┐    ┌───────────────┐
│ AuthMe_Core   │    │  AuthMe_OTP   │    │ AuthMe_Email  │
│  (Hooks/UI)   │    │  (6-digit OTP)│    │   (Sending)   │
└───────────────┘    └───────────────┘    └───────────────┘
        │                     │                     │
        └─────────────────────┼─────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   AuthMe_DB                                  │
│              (Database Operations)                           │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              wp_authme_otp_storage                           │
│           (Custom Database Table)                            │
└─────────────────────────────────────────────────────────────┘
```

### Class Reference

| Class | File | Purpose |
|-------|------|---------|
| `AuthMe_Core` | `includes/class-authme-core.php` | Plugin initialization, hook registration, UI injection |
| `AuthMe_Auth` | `includes/class-authme-auth.php` | User registration, login, password reset, real-time validation |
| `AuthMe_OTP` | `includes/class-authme-otp.php` | OTP generation, storage, verification, cleanup |
| `AuthMe_DB` | `includes/class-authme-db.php` | Database table creation and management |
| `AuthMe_Host_Request` | `includes/class-authme-host-request.php` | Host application submission, document upload |
| `AuthMe_Admin` | `includes/class-authme-admin.php` | Admin menu, dashboard, host request management |

### File Structure

```
authme/
├── authme.php                    # MAIN PLUGIN ENTRY POINT
│                                   # Plugin initialization, constants, includes
├── index.php                     # Security: Prevents directory browsing
│
├── includes/                     # PHP CORE CLASSES
│   ├── class-authme-core.php     # Hooks, filters, UI injection, activation
│   ├── class-authme-auth.php     # Authentication: login, register, validation
│   ├── class-authme-otp.php      # OTP: generate, send, verify, cleanup
│   ├── class-authme-db.php       # Database: table creation, management
│   ├── class-authme-host-request.php  # Host application handling
│   ├── class-authme-admin.php    # WordPress admin panel
│   ├── db-schema.php             # Database schema registry (SINGLE SOURCE OF TRUTH)
│   ├── assets-loader-raw.php     # Asset loading utilities
│   └── assets/
│       └── am-phone-core.js      # International phone validation library
│
├── frontend/                     # FRONTEND INTERFACE
│   ├── template/
│   │   ├── overlay.php           # Main popup container
│   │   ├── login.php             # Login form template
│   │   ├── register.php          # Registration form template
│   │   ├── otp.php               # OTP verification screen
│   │   ├── forgot-password.php   # Password reset request
│   │   ├── new-password.php      # New password form
│   │   ├── host-request.php      # Become a Host modal
│   │   └── email-*.php           # Email HTML templates
│   └── js/
│       ├── global.js             # Shared utilities, AJAX, validation
│       ├── overlay.js            # Popup open/close, screen switching
│       ├── login.js              # Login form logic
│       ├── register.js           # Registration with real-time validation
│       ├── otp.js                # OTP input handling, countdown timer
│       ├── forgot-password.js    # Password reset flow
│       ├── new-password.js        # New password form
│       └── host-request.js        # Multi-step host application
│
├── backend/                      # WORDPRESS ADMIN PANEL
│   ├── template/
│   │   ├── dashboard.php         # Main admin dashboard
│   │   ├── database.php          # Database management page
│   │   ├── host-requests.php     # Host applications list
│   │   ├── users/all-users.php   # User management
│   │   └── users/view-user.php   # View user details
│   └── js/
│       ├── dashboard.js          # Dashboard functionality
│       ├── database.js           # Database operations
│       ├── host-requests.js       # Host request management
│       └── users/*.js             # User management scripts
│
├── components/                  # SHARED COMPONENTS
│   ├── global/
│   │   ├── global.js             # Global frontend utilities
│   │   └── am-phone-core.js      # Phone validation library
│   ├── google-auth/
│   │   ├── google-auth.php       # Google OAuth handler
│   │   └── google-auth.js        # Google login JavaScript
│   ├── confirmation/
│   │   ├── confirmation.php      # Confirmation modal
│   │   └── confirmation.js       # Confirmation JavaScript
│   └── toaster/
│       ├── toaster.php           # Toast notification container
│       └── toaster.js            # Toast notification JavaScript
│
├── mails/                        # EMAIL SYSTEM
│   ├── mail-controller.php       # Email sending logic
│   └── master-mail-template.php  # Email HTML template
│
└── config/                       # CONFIGURATION
    └── mail-config.php           # Email template configurations
```

### Database Schema

AuthMe creates two custom database tables:

#### Table 1: `wp_authme_otp_storage`

Stores OTP codes for registration, login verification, and password reset flows.

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| email | VARCHAR(100) | Recipient email address |
| otp_code | VARCHAR(6) | 6-digit OTP code |
| purpose | VARCHAR(20) | Purpose: registration, login, password_reset, host_request |
| created_at | TIMESTAMP | When OTP was created |
| expires_at | TIMESTAMP | When OTP expires (created_at + 60 seconds) |
| is_verified | TINYINT(1) | 0 = pending, 1 = verified |
| user_data | TEXT | JSON blob for temporary user data during registration |

#### Table 2: `wp_host_request`

Stores "Become a Host" application submissions.

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| user_data | LONGTEXT | JSON blob with applicant data (name, username, email, mobile, documents) |
| status | VARCHAR(50) | Status: pending, approved, rejected |
| date | DATETIME | When application was submitted |

### AJAX Actions Reference

#### Authentication AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_check_username` | `AuthMe_Auth::ajax_check_username` | Check username availability in real-time |
| `authme_check_email` | `AuthMe_Auth::ajax_check_email` | Check email availability in real-time |
| `authme_check_mobile` | `AuthMe_Auth::ajax_check_mobile` | Check mobile number availability |
| `authme_check_user_exists` | `AuthMe_Auth::ajax_check_user_exists` | Check if user exists for login |
| `authme_login_user` | `AuthMe_Auth::ajax_login_user` | Authenticate and login user |
| `authme_register_user` | `AuthMe_Auth::ajax_register_user` | Create new WordPress user (after OTP verification) |
| `authme_forgot_check_user` | `AuthMe_Auth::ajax_forgot_check_user` | Check user for password reset |
| `authme_reset_password` | `AuthMe_Auth::ajax_reset_password` | Reset user password (after OTP verification) |

#### OTP AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_send_otp` | `AuthMe_OTP::ajax_send_otp` | Generate and send OTP via email |
| `authme_verify_otp` | `AuthMe_OTP::ajax_verify_otp` | Verify OTP code |
| `authme_resend_otp` | `AuthMe_OTP::ajax_resend_otp` | Resend OTP (invalidate previous, generate new) |

#### Host Request AJAX Actions

| Action | Handler | Description |
|--------|---------|-------------|
| `authme_check_host_username` | `AuthMe_Host_Request::ajax_check_host_username` | Check username for host application |
| `authme_check_host_email` | `AuthMe_Host_Request::ajax_check_host_email` | Check email for host application |
| `authme_check_host_mobile` | `AuthMe_Host_Request::ajax_check_host_mobile` | Check mobile for host application |
| `authme_upload_host_document` | `AuthMe_Host_Request::ajax_upload_host_document` | Upload host document (Aadhar, PAN) |
| `authme_submit_host_request` | `AuthMe_Host_Request::ajax_submit_host_request` | Submit complete host application |

### Hooks & Filters

#### Actions

| Hook | Description |
|------|-------------|
| `authme_otp_cleanup` | Scheduled cron action for cleaning expired OTPs (runs daily at 3:00 AM) |
| `wp_enqueue_scripts` | Enqueue frontend CSS and JavaScript files |
| `wp_footer` | Inject overlay HTML, toaster, and confirmation modals |
| `admin_menu` | Register AuthMe admin menu items |
| `admin_enqueue_scripts` | Enqueue admin panel assets |
| `init` | Register AJAX actions, rewrite rules |
| `template_redirect` | Handle virtual page (/?become-host), universal logout |

#### Filters

| Hook | Description |
|------|-------------|
| `query_vars` | Register custom query variables for virtual pages |
| `plugin_action_links_{plugin_basename}` | Add "Settings" link to plugins page |
| `login_redirect` | Restrict login redirect for non-admin users |

### Constants

| Constant | Description |
|----------|-------------|
| `AUTHME_VERSION` | Plugin version number (2.1.9) |
| `AUTHME_PLUGIN_DIR` | Server file path to plugin directory |
| `AUTHME_PLUGIN_URL` | URL to plugin directory |
| `AUTHME_PLUGIN_BASENAME` | Plugin basename identifier |

---

## Admin Dashboard

Access via WordPress admin menu: **AuthMe**

### Dashboard Page
- Plugin information and current version
- Quick start instructions with trigger URLs and JavaScript functions
- Quick links to other admin pages
- Security status overview

### Database Management
- View table creation status
- View table column details
- Create/update tables manually if needed
- AJAX-powered status checks

### Host Requests
- View all host applications (filter by pending/approved/rejected)
- Search functionality
- Pagination support
- View application details (personal info, uploaded documents)
- Approve/Reject actions with automatic email notifications

### User Management
- View all registered users
- Filter by user role (Traveller/Host)
- View individual user details
- User metadata display

---

## Security Features

| Feature | Implementation |
|---------|----------------|
| **OTP Verification** | Required for registration and password reset (60-second validity) |
| **Nonce Validation** | All AJAX requests use WordPress security nonces |
| **Password Hashing** | Uses WordPress password hashing (`wp_hash_password`) |
| **Admin Protection** | Administrators cannot use the frontend popup for login |
| **Input Sanitization** | All user inputs are properly sanitized |
| **Automatic Cleanup** | Cron job removes expired/verified OTPs daily |
| **File Upload Security** | Only JPEG images allowed, 1MB size limit, processed via `wp_handle_upload` |
| **Email Authentication** | Password change and login alerts sent to user email |

---

## Troubleshooting

### Plugin Not Working After Upload

1. Verify `am-phone-core.js` exists in `includes/assets/`
2. Check PHP version is 7.4 or higher
3. Verify WordPress version is 5.0 or higher
4. Ensure `json` and `mbstring` PHP extensions are enabled

### OTP Not Being Sent

1. Check WordPress can send emails (install WP Mail SMTP plugin if needed)
2. Verify the recipient email address is correct
3. Check spam/junk folders
4. Ensure `wp_authme_otp_storage` table exists (go to AuthMe > Database)

### Database Table Not Created

1. Go to **WordPress Admin > AuthMe > Database**
2. Click "Create/Update Table"
3. Check PHP error logs for database errors

### Overlay Not Appearing

1. Ensure you're logged out (popup only shows for non-logged-in users)
2. Check browser console for JavaScript errors
3. Verify assets are loading in the network tab
4. Try visiting `/authme` URL directly

### Host Request Issues

1. Ensure all required documents are uploaded (Aadhar front, Aadhar back, PAN)
2. Only JPEG images are allowed (maximum 1MB per file)
3. After submission, check admin panel for pending requests
4. Admin can approve/reject; approved requests auto-create user with "Host" role

---

## Support

For issues, bugs, or feature requests, contact the author:

**Website:** https://arttechfuzion.com  
**Email:** Contact via website

---

## License

This plugin is proprietary software by **Art-Tech Fuzion**.

All rights reserved. No part of this software may be reproduced, distributed, or transmitted in any form or by any means without prior written permission from the author.