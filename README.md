# AuthMe - Vacation Rental Authentication Plugin

AuthMe is a secure WordPress plugin that adds a beautiful popup authentication system to your vacation rental website. It allows users to register, login, and reset passwords through a modern popup overlay instead of the default WordPress login page. The plugin includes OTP email verification to ensure only real users can create accounts, supports two user roles (Traveller and Host), and includes a complete host application system where property owners can apply to list their properties.

---

## Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [Who Should Use AuthMe?](#who-should-use-authme)
4. [System Requirements](#system-requirements)
5. [Installation](#installation)
6. [How It Works](#how-it-works)
   - [Registration Flow](#registration-flow)
   - [Login Flow](#login-flow)
   - [Password Reset Flow](#password-reset-flow)
   - [Become a Host Flow](#become-a-host-flow)
   - [Google Authentication](#google-authentication)
7. [Admin Dashboard](#admin-dashboard)
8. [Developer Guide](#developer-guide)
   - [File Structure](#file-structure)
   - [Database Schema](#database-schema)
   - [AJAX Actions](#ajax-actions)
   - [Hooks & Filters](#hooks--filters)
   - [Constants](#constants)
9. [Security Features](#security-features)
10. [Troubleshooting](#troubleshooting)
11. [Support](#support)
12. [License](#license)

---

## Overview

AuthMe is a comprehensive WordPress authentication plugin designed specifically for vacation rental websites. It replaces the default WordPress login and registration pages with a beautiful, responsive popup overlay that works seamlessly across all devices.

The plugin introduces two custom user roles: "Traveller" for guests who want to book properties, and "Host" for property owners who want to list their properties. Users can apply to become a host by submitting their details and verification documents through a multi-step form. The admin then reviews these applications and can approve or reject them.

Key highlights:
- Beautiful popup authentication system
- OTP email verification for secure registration
- Two built-in user roles (Traveller and Host)
- Complete host application and document upload system
- Google OAuth integration for one-click login
- Real-time validation for username, email, and phone
- International phone number support for 150+ countries

---

## Key Features

| Feature | Description |
|---------|-------------|
| Popup Authentication | Beautiful modal popup for login, register, and password reset |
| OTP Email Verification | 6-digit one-time password sent via email |
| Real-time Validation | Instant feedback on username and email availability |
| Password Strength Meter | Visual indicator showing password security level |
| International Phone Validation | Phone validation for 150+ countries |
| Custom User Roles | "Traveller" for guests, "Host" for property owners |
| Host Application System | Multi-step form with document upload (Aadhar, PAN) |
| Google Authentication | One-click login via Google OAuth |
| Admin Dashboard | Manage users, host requests, and database |
| Email Notifications | Beautiful HTML email templates |
| Automatic Cleanup | Scheduled cron job removes expired OTPs |
| Security Protection | Nonce validation, password hashing, input sanitization |

---

## Who Should Use AuthMe?

AuthMe is ideal for:

- Vacation Rental Platforms (like Airbnb, VRBO, Booking.com)
- Property Listing Websites
- Travel and Tourism Websites
- Multi-vendor Marketplaces
- Hospitality Service Providers (hotels, resorts, homestays)

Use AuthMe if your website needs:
- Secure user registration with email verification
- Separate user roles for guests and property owners
- A modern popup authentication system
- Host application and approval workflow
- International phone number support

---

## System Requirements

| Requirement | Minimum Version |
|-------------|-----------------|
| WordPress | 5.0 or higher |
| PHP | 7.4 or higher |
| PHP Extensions | json, mbstring |
| MySQL | 5.0 or higher |

---

## Installation

### Method 1: Upload via WordPress Admin

1. Log in to your WordPress admin panel
2. Go to **Plugins > Add New**
3. Click **Upload Plugin** and select the AuthMe zip file
4. Click **Install Now** and then **Activate**

### Method 2: Manual Upload

1. Download the AuthMe plugin folder
2. Upload the `authme` folder to `/wp-content/plugins/` directory
3. Activate the plugin through the **Plugins** menu in WordPress

### After Activation

The plugin will automatically:
- Create required database tables
- Register two custom user roles ("Traveller" and "Host")
- Schedule a daily cron job for OTP cleanup
- Flush rewrite rules for virtual pages

---

## How It Works

### Registration Flow

1. User clicks "Register" button
2. Registration form appears with username, email, mobile, and password fields
3. User clicks "Send OTP"
   - 6-digit OTP is generated and sent to email
   - User data is stored temporarily in database
4. OTP verification screen appears with 60-second countdown timer
5. User enters OTP and clicks "Verify & Proceed"
6. If OTP is valid, WordPress user is created with "Traveller" role
7. User is automatically logged in

### Login Flow

1. User clicks "Login" button
2. Login form appears with email/username and password fields
3. User enters credentials and clicks "Login"
4. System validates credentials via WordPress authentication
5. On success, user is logged in and redirected to homepage

### Password Reset Flow

1. User clicks "Forgot Password?" link
2. User enters their email/username
3. User clicks "Send Reset OTP"
4. OTP is sent to user's email
5. User enters OTP and verifies
6. User enters new password and confirms
7. Password is updated and user can login with new password

### Become a Host Flow

1. User visits `/?become-host` URL or clicks "Become a Host"
2. **Step 1:** Fill personal information (username, full name, email, mobile)
3. **Step 2:** Upload verification documents (Aadhar front/back, PAN card)
4. **Step 3:** Verify email with OTP
5. **Step 4:** Application submitted successfully

**Admin Process:**
- Admin receives notification in dashboard
- Admin reviews application and documents
- Admin approves or rejects the application
- If approved, new user created with "Host" role and credentials sent via email
- If rejected, email notification sent to applicant

### Google Authentication

- Users can click "Continue with Google" on the login screen
- Requires Google Client ID configuration in admin panel
- Automatically creates "Traveller" user account upon first login
- Security alerts sent via email for new Google logins

---

## Admin Dashboard

Access via WordPress admin menu: **AuthMe**

### Dashboard
- Plugin information and version
- Quick start instructions and trigger URLs
- Quick links to other admin pages
- Security status overview

### Database Management
- View table status and columns
- Create/update tables manually if needed

### Host Requests
- View all applications (filter by pending/approved/rejected)
- Search and pagination support
- View application details and documents
- Approve/Reject with email notifications

### User Management
- View all registered users
- Filter by user role (Traveller/Host)
- View individual user details

---

## Developer Guide

### File Structure

```
authme/
├── authme.php                    # Main plugin entry point
├── index.php                     # Security file
│
├── includes/                     # PHP core classes
│   ├── class-authme-core.php     # Hooks, filters, UI injection
│   ├── class-authme-auth.php    # Login, register, validation
│   ├── class-authme-otp.php     # OTP generate, send, verify
│   ├── class-authme-db.php      # Database operations
│   ├── class-authme-host-request.php  # Host application handling
│   ├── class-authme-admin.php   # Admin panel
│   ├── db-schema.php            # Database schema definitions
│   └── assets/
│       └── am-phone-core.js     # Phone validation library
│
├── frontend/                     # Frontend interface
│   ├── template/                 # HTML templates
│   │   ├── overlay.php          # Main popup container
│   │   ├── login.php            # Login form
│   │   ├── register.php         # Registration form
│   │   ├── otp.php              # OTP verification
│   │   ├── forgot-password.php  # Password reset
│   │   ├── new-password.php     # New password form
│   │   └── host-request.php      # Become a Host modal
│   └── js/                      # Frontend JavaScript
│       ├── global.js            # Shared utilities
│       ├── overlay.js           # Popup management
│       ├── login.js             # Login logic
│       ├── register.js          # Registration logic
│       ├── otp.js               # OTP handling
│       ├── forgot-password.js   # Password reset
│       └── host-request.js      # Host application
│
├── backend/                      # WordPress admin panel
│   ├── template/                 # Admin templates
│   └── js/                      # Admin JavaScript
│
├── components/                  # Shared components
│   ├── global/                  # Global utilities
│   ├── google-auth/             # Google OAuth
│   ├── confirmation/           # Confirmation modal
│   └── toaster/                 # Toast notifications
│
├── mails/                        # Email system
│   ├── mail-controller.php      # Email sending logic
│   └── master-mail-template.php # Email template
│
└── config/                       # Configuration
    └── mail-config.php          # Email configs
```

### Database Schema

The plugin creates two custom database tables:

#### wp_authme_otp_storage

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| email | VARCHAR(100) | Recipient email |
| otp_code | VARCHAR(6) | 6-digit OTP code |
| purpose | VARCHAR(20) | registration, login, password_reset, host_request |
| created_at | TIMESTAMP | When OTP was created |
| expires_at | TIMESTAMP | When OTP expires |
| is_verified | TINYINT(1) | 0 = pending, 1 = verified |
| user_data | TEXT | JSON blob for temporary user data |

#### wp_host_request

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) AUTO_INCREMENT | Primary key |
| user_data | LONGTEXT | JSON blob with applicant data |
| status | VARCHAR(50) | pending, approved, rejected |
| date | DATETIME | Application submission date |

### AJAX Actions

#### Authentication Actions

| Action | Description |
|--------|-------------|
| authme_check_username | Check username availability |
| authme_check_email | Check email availability |
| authme_check_mobile | Check mobile number availability |
| authme_check_user_exists | Check if user exists for login |
| authme_login_user | Authenticate and login user |
| authme_register_user | Create new user after OTP verification |
| authme_forgot_check_user | Check user for password reset |
| authme_reset_password | Reset password after OTP verification |

#### OTP Actions

| Action | Description |
|--------|-------------|
| authme_send_otp | Generate and send OTP |
| authme_verify_otp | Verify OTP code |
| authme_resend_otp | Resend new OTP |

#### Host Request Actions

| Action | Description |
|--------|-------------|
| authme_check_host_username | Check username for host application |
| authme_check_host_email | Check email for host application |
| authme_check_host_mobile | Check mobile for host application |
| authme_upload_host_document | Upload verification document |
| authme_submit_host_request | Submit complete application |

### Hooks & Filters

#### Actions

| Hook | Description |
|------|-------------|
| authme_otp_cleanup | Cron action for cleaning expired OTPs |
| wp_enqueue_scripts | Enqueue frontend CSS/JS |
| wp_footer | Inject overlay HTML and modals |
| admin_menu | Register admin menus |
| init | Register AJAX actions and rewrite rules |
| template_redirect | Handle virtual pages |

#### Filters

| Hook | Description |
|------|-------------|
| query_vars | Register custom query variables |
| plugin_action_links_{plugin_basename} | Add settings link to plugins page |
| login_redirect | Customize login redirect |

### Constants

| Constant | Description |
|----------|-------------|
| AUTHME_VERSION | Plugin version number |
| AUTHME_PLUGIN_DIR | Server file path to plugin directory |
| AUTHME_PLUGIN_URL | URL to plugin directory |
| AUTHME_PLUGIN_BASENAME | Plugin basename identifier |

---

## Security Features

| Feature | Description |
|---------|-------------|
| OTP Verification | Required for registration and password reset (60-second validity) |
| Nonce Validation | All AJAX requests use WordPress security nonces |
| Password Hashing | Uses WordPress password hashing |
| Admin Protection | Administrators cannot use frontend popup for login |
| Input Sanitization | All user inputs are properly sanitized |
| Automatic Cleanup | Cron job removes expired/verified OTPs daily |
| File Upload Security | Only JPEG images, 1MB limit, processed via wp_handle_upload |
| Email Alerts | Password change and login alerts sent to user email |

---

## Troubleshooting

### Plugin Not Working

1. Verify am-phone-core.js exists in includes/assets/
2. Check PHP version is 7.4 or higher
3. Verify WordPress version is 5.0 or higher
4. Ensure json and mbstring PHP extensions are enabled

### OTP Not Being Sent

1. Check WordPress can send emails (install WP Mail SMTP plugin)
2. Verify recipient email address is correct
3. Check spam/junk folders
4. Ensure wp_authme_otp_storage table exists

### Database Table Not Created

1. Go to AuthMe > Database in admin panel
2. Click "Create/Update Table"
3. Check PHP error logs for database errors

### Overlay Not Appearing

1. Ensure you are logged out (popup only shows for non-logged-in users)
2. Check browser console for JavaScript errors
3. Verify assets are loading in network tab
4. Try visiting /authme URL directly

### Host Request Issues

1. Ensure all required documents are uploaded
2. Only JPEG images allowed (max 1MB per file)
3. Check admin panel for pending requests after submission

---

## Support

For issues and feature requests, contact:

**Website:** https://arttechfuzion.com

---

## License

This plugin is proprietary software by **Art-Tech Fuzion**. All rights reserved.