<?php

/**
 * Plugin Name: AuthMe
 * Plugin URI: https://arttechfuzion.com
 * Description: A comprehensive WordPress authentication plugin with OTP verification for secure registration and login.
 * Version: 2.0.0
 * Author: Art-Tech Fuzion
 * Author URI: https://arttechfuzion.com
 * Text Domain: authme
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/* ──────────────────────────────────────────────
 * Constants
 * ────────────────────────────────────────────── */
define('AUTHME_VERSION', '2.0.0');
define('AUTHME_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AUTHME_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AUTHME_PLUGIN_BASENAME', plugin_basename(__FILE__));

/* ──────────────────────────────────────────────
 * Composer Autoload (libphonenumber-for-php)
 * ────────────────────────────────────────────── */
require_once AUTHME_PLUGIN_DIR . 'vendor/autoload.php';

/* ──────────────────────────────────────────────
 * Include Files
 * ────────────────────────────────────────────── */
require_once AUTHME_PLUGIN_DIR . 'includes/assets-loader.php';
require_once AUTHME_PLUGIN_DIR . 'includes/db-schema.php';
require_once AUTHME_PLUGIN_DIR . 'includes/class-authme-db.php';
require_once AUTHME_PLUGIN_DIR . 'includes/class-authme-email.php';
require_once AUTHME_PLUGIN_DIR . 'includes/class-authme-otp.php';
require_once AUTHME_PLUGIN_DIR . 'includes/class-authme-auth.php';
require_once AUTHME_PLUGIN_DIR . 'includes/class-authme-host-request.php';

// Admin files (only on admin pages)
if (is_admin()) {
    require_once AUTHME_PLUGIN_DIR . 'admin/class-authme-admin.php';
}

/* ──────────────────────────────────────────────
 * Plugin Activation
 * ────────────────────────────────────────────── */
register_activation_hook(__FILE__, 'authme_activate_plugin');

function authme_activate_plugin()
{
    // Register the 'traveller' role if it doesn't exist
    if (! get_role('traveller')) {
        add_role('traveller', 'Traveller', array(
            'read' => true,
        ));
    }
    
    // Register the 'host' role if it doesn't exist
    if (! get_role('host')) {
        add_role('host', 'Host', array(
            'read' => true,
            'upload_files' => true,
        ));
    }

    // Register rewrite rules and flush so /authme URL works immediately
    authme_register_rewrite_rules();
    flush_rewrite_rules();

    // Schedule OTP cleanup cron (runs once daily at 3 AM Site Local Time)
    if (! wp_next_scheduled('authme_otp_cleanup')) {
        // Calculate the next occurrence of 3:00 AM in the site's local timezone
        $timezone = wp_timezone();
        $date = new DateTime('today 03:00:00', $timezone);
        
        // If 3:00 AM today has already passed, schedule for tomorrow
        if ($date->getTimestamp() <= time()) {
            $date->modify('+1 day');
        }
        
        wp_schedule_event($date->getTimestamp(), 'daily', 'authme_otp_cleanup');
    }
}



/* ──────────────────────────────────────────────
 * Plugin Deactivation
 * ────────────────────────────────────────────── */
register_deactivation_hook(__FILE__, 'authme_deactivate_plugin');

function authme_deactivate_plugin()
{
    flush_rewrite_rules();

    // Unschedule OTP cleanup cron
    $timestamp = wp_next_scheduled('authme_otp_cleanup');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'authme_otp_cleanup');
    }
}

/* ──────────────────────────────────────────────
 * OTP Auto-Cleanup Cron Hook
 *
 * Runs twice daily to delete expired/verified OTPs
 * from the wp_authme_otp_storage table.
 * Scheduled on activation, unscheduled on deactivation.
 * ────────────────────────────────────────────── */
add_action('authme_otp_cleanup', 'authme_run_otp_cleanup');

function authme_run_otp_cleanup()
{
    $otp = new AuthMe_OTP();
    $otp->cleanup_expired_otps();

    $host_req = new AuthMe_Host_Request();
    $host_req->cleanup_orphaned_documents();
    $host_req->cleanup_rejected_requests();
}

/* ──────────────────────────────────────────────
 * Enqueue Frontend Assets
 *
 * All file paths are centralized in:
 * includes/assets-loader.php → AuthMe_Assets_Loader
 * ────────────────────────────────────────────── */
add_action('wp_enqueue_scripts', array('AuthMe_Assets_Loader', 'enqueue_frontend'));

/* ──────────────────────────────────────────────
 * Auto-inject Overlay via wp_footer
 *
 * The overlay HTML is injected into every frontend page
 * at the bottom of <body>. It starts hidden (display:none)
 * and is opened via JS when needed.
 *
 * If the URL has ?authme_open=1 (from /authme redirect),
 * the overlay auto-opens on page load.
 * ────────────────────────────────────────────── */
add_action('wp_footer', 'authme_inject_overlay_in_footer');

function authme_inject_overlay_in_footer()
{
    // Don't inject for logged-in users
    if (is_user_logged_in()) {
        return;
    }

    // Include the toaster + overlay templates
    include AUTHME_PLUGIN_DIR . 'frontend/templates/toaster.php';
    include AUTHME_PLUGIN_DIR . 'frontend/templates/overlay.php';

    // Auto-open if ?authme_open=1 is in the URL (from /authme redirect)
    if (isset($_GET['authme_open']) && $_GET['authme_open'] === '1') {
        echo '<script>document.addEventListener("DOMContentLoaded",function(){if(typeof authmeOpenOverlay==="function"){authmeOpenOverlay();}});</script>';
    }
}

/* ──────────────────────────────────────────────
 * Auto-inject Host Request Modal via wp_footer
 * ────────────────────────────────────────────── */
add_action('wp_footer', 'authme_inject_host_request_modal');

function authme_inject_host_request_modal()
{
    // If the user is already logged in and has the 'host' role, do not show the modal.
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('host', (array) $user->roles)) {
            return;
        }
    }

    // The Host Request modal is available for both logged-in and guest users.
    // It only gets injected and auto-opened if ?become-host is in the URL to save DOM size.
    if (isset($_GET['become-host'])) {
        include AUTHME_PLUGIN_DIR . 'frontend/templates/toaster.php'; // In case it's not already included
        include AUTHME_PLUGIN_DIR . 'frontend/templates/host-request.php';
        echo '<script>document.addEventListener("DOMContentLoaded",function(){if(typeof authmeOpenHostModal==="function"){authmeOpenHostModal();}});</script>';
    }
}

/* ──────────────────────────────────────────────
 * Register AJAX Endpoints
 * ────────────────────────────────────────────── */
$authme_auth = new AuthMe_Auth();
$authme_otp  = new AuthMe_OTP();
$authme_host = new AuthMe_Host_Request();

// Public (nopriv) + logged-in AJAX actions
$ajax_actions = array(
    'authme_check_username'    => array($authme_auth, 'ajax_check_username'),
    'authme_check_email'       => array($authme_auth, 'ajax_check_email'),
    'authme_check_mobile'      => array($authme_auth, 'ajax_check_mobile'),
    'authme_check_user_exists' => array($authme_auth, 'ajax_check_user_exists'),
    'authme_login_user'        => array($authme_auth, 'ajax_login_user'),
    'authme_register_user'     => array($authme_auth, 'ajax_register_user'),
    'authme_complete_login'    => array($authme_auth, 'ajax_complete_login'),
    'authme_forgot_check_user' => array($authme_auth, 'ajax_forgot_check_user'),
    'authme_reset_password'    => array($authme_auth, 'ajax_reset_password'),
    'authme_send_otp'          => array($authme_otp, 'ajax_send_otp'),
    'authme_verify_otp'        => array($authme_otp, 'ajax_verify_otp'),
    'authme_check_host_username'   => array($authme_host, 'ajax_check_host_username'),
    'authme_check_host_email'      => array($authme_host, 'ajax_check_host_email'),
    'authme_check_host_mobile'     => array($authme_host, 'ajax_check_host_mobile'),
    'authme_upload_host_document'  => array($authme_host, 'ajax_upload_host_document'),
    'authme_submit_host_request'   => array($authme_host, 'ajax_submit_host_request'),
    'authme_delete_host_document'  => array($authme_host, 'ajax_delete_host_document'),
);

foreach ($ajax_actions as $action => $callback) {
    add_action('wp_ajax_' . $action, $callback);
    add_action('wp_ajax_nopriv_' . $action, $callback);
}

/* ──────────────────────────────────────────────
 * Settings Link on Plugins Page
 * ────────────────────────────────────────────── */
add_filter('plugin_action_links_' . AUTHME_PLUGIN_BASENAME, 'authme_add_settings_link');

function authme_add_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('admin.php?page=authme') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

/* ──────────────────────────────────────────────
 * Virtual /authme Page Endpoint
 *
 * When a user visits yoursite.com/authme, they are
 * redirected to the homepage with ?authme_open=1.
 * The wp_footer hook detects this parameter and
 * auto-opens the overlay — no blank page.
 * ────────────────────────────────────────────── */

/**
 * Register rewrite rule: /authme → index.php?authme_page=1
 */
function authme_register_rewrite_rules()
{
    add_rewrite_rule('^authme/?$', 'index.php?authme_page=1', 'top');
}
add_action('init', 'authme_register_rewrite_rules');

/**
 * Register 'authme_page' as a recognized query variable.
 */
function authme_register_query_vars($vars)
{
    $vars[] = 'authme_page';
    return $vars;
}
add_filter('query_vars', 'authme_register_query_vars');

/**
 * Handle the /authme virtual page:
 * - If logged in → redirect to homepage (no popup needed)
 * - If not logged in → redirect to homepage with ?authme_open=1
 *   The footer hook auto-opens the popup on the real page.
 */
function authme_handle_virtual_page()
{
    if (! get_query_var('authme_page')) {
        return;
    }

    if (is_user_logged_in()) {
        wp_safe_redirect(home_url());
        exit;
    }

// Redirect to homepage with auto-open trigger parameter
    wp_safe_redirect(home_url('?authme_open=1'));
    exit;
}
add_action('template_redirect', 'authme_handle_virtual_page');

/**
 * Handle universal static logout URL: /?authme_logout=1
 */
function authme_handle_universal_logout()
{
    if (isset($_GET['authme_logout']) && $_GET['authme_logout'] === '1') {
        if (is_user_logged_in()) {
            wp_logout();
        }
        wp_safe_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'authme_handle_universal_logout');

/* ──────────────────────────────────────────────
 * UI & Access Restrictions
 * ────────────────────────────────────────────── */

/**
 * Hide the WordPress Admin Bar for non-administrators on the frontend.
 */
add_action('after_setup_theme', 'authme_disable_admin_bar');
function authme_disable_admin_bar() {
    if (is_user_logged_in() && !current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

/**
 * Redirect non-admins to the homepage after login via wp-login.php.
 */
add_filter('login_redirect', 'authme_restrict_login_redirect', 10, 3);
function authme_restrict_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (!in_array('administrator', $user->roles)) {
            return home_url();
        }
    }
    return $redirect_to;
}

/**
 * Restrict wp-admin access to Administrators only.
 * This blocks direct access to dashboard pages for non-admins.
 */
add_action('admin_init', 'authme_restrict_admin_access');
function authme_restrict_admin_access() {
    // Allow AJAX requests for plugin functionality
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    // If user is logged in but not an administrator, redirect to home page
    if (is_user_logged_in() && !current_user_can('administrator')) {
        wp_safe_redirect(home_url());
        exit;
    }
}


