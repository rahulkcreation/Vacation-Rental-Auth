<?php
/**
 * AuthMe — Centralized Assets Loader
 *
 * This file is the SINGLE source of truth for all file paths
 * (CSS, JS, templates, includes) used across the plugin.
 * Every other file references paths from here.
 *
 * Directory Structure (after rearrangement):
 *   frontend/assets/css/   — All frontend CSS files
 *   frontend/assets/js/    — All frontend JS files
 *   frontend/templates/    — All frontend template files
 *   includes/assets/       — Global CSS (shared variables)
 *   includes/              — PHP class files
 *   admin/assets/          — Admin CSS/JS files
 *   admin/templates/       — Admin template files
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AuthMe_Assets_Loader {

    /* ================================================
     * 📁 PATH REGISTRY
     * ================================================
     * All plugin file paths are defined here.
     * Use AuthMe_Assets_Loader::get() to access them.
     * ================================================ */

    /**
     * Get all registered file paths.
     *
     * Each entry contains:
     *   'dir'  → Absolute server path (for file_exists / filemtime)
     *   'url'  → Public URL (for wp_enqueue)
     *
     * @return array Associative array of all file paths.
     */
    public static function get_paths() {

        $base_dir = AUTHME_PLUGIN_DIR;
        $base_url = AUTHME_PLUGIN_URL;

        return array(

            /* ── Global CSS (shared variables) ──── */
            'css_global'    => array(
                'dir' => $base_dir . 'includes/assets/global.css',
                'url' => $base_url . 'includes/assets/global.css',
            ),

            /* ── Frontend CSS Files ─────────────── */
            'css_overlay'   => array(
                'dir' => $base_dir . 'frontend/assets/css/overlay.css',
                'url' => $base_url . 'frontend/assets/css/overlay.css',
            ),
            'css_login'     => array(
                'dir' => $base_dir . 'frontend/assets/css/login.css',
                'url' => $base_url . 'frontend/assets/css/login.css',
            ),
            'css_register'  => array(
                'dir' => $base_dir . 'frontend/assets/css/register.css',
                'url' => $base_url . 'frontend/assets/css/register.css',
            ),
            'css_forgot_password' => array(
                'dir' => $base_dir . 'frontend/assets/css/forgot-password.css',
                'url' => $base_url . 'frontend/assets/css/forgot-password.css',
            ),
            'css_new_password'    => array(
                'dir' => $base_dir . 'frontend/assets/css/new-password.css',
                'url' => $base_url . 'frontend/assets/css/new-password.css',
            ),
            'css_host_request' => array(
                'dir' => $base_dir . 'frontend/assets/css/host-request.css',
                'url' => $base_url . 'frontend/assets/css/host-request.css',
            ),
            'css_otp'       => array(
                'dir' => $base_dir . 'frontend/assets/css/otp.css',
                'url' => $base_url . 'frontend/assets/css/otp.css',
            ),
            'css_toaster'   => array(
                'dir' => $base_dir . 'frontend/assets/css/toaster.css',
                'url' => $base_url . 'frontend/assets/css/toaster.css',
            ),

            /* ── Frontend JS Files ──────────────── */
            'js_global'     => array(
                'dir' => $base_dir . 'frontend/assets/js/global.js',
                'url' => $base_url . 'frontend/assets/js/global.js',
            ),
            'js_toaster'    => array(
                'dir' => $base_dir . 'frontend/assets/js/toaster.js',
                'url' => $base_url . 'frontend/assets/js/toaster.js',
            ),
            'js_overlay'    => array(
                'dir' => $base_dir . 'frontend/assets/js/overlay.js',
                'url' => $base_url . 'frontend/assets/js/overlay.js',
            ),
            'js_login'      => array(
                'dir' => $base_dir . 'frontend/assets/js/login.js',
                'url' => $base_url . 'frontend/assets/js/login.js',
            ),
            'js_country_phone_regex' => array(
                'dir' => $base_dir . 'frontend/assets/js/country-phone-regex.js',
                'url' => $base_url . 'frontend/assets/js/country-phone-regex.js',
            ),
            'js_register'   => array(
                'dir' => $base_dir . 'frontend/assets/js/register.js',
                'url' => $base_url . 'frontend/assets/js/register.js',
            ),
            'js_forgot_password' => array(
                'dir' => $base_dir . 'frontend/assets/js/forgot-password.js',
                'url' => $base_url . 'frontend/assets/js/forgot-password.js',
            ),
            'js_new_password'    => array(
                'dir' => $base_dir . 'frontend/assets/js/new-password.js',
                'url' => $base_url . 'frontend/assets/js/new-password.js',
            ),
            'js_host_request' => array(
                'dir' => $base_dir . 'frontend/assets/js/host-request.js',
                'url' => $base_url . 'frontend/assets/js/host-request.js',
            ),
            'js_otp'        => array(
                'dir' => $base_dir . 'frontend/assets/js/otp.js',
                'url' => $base_url . 'frontend/assets/js/otp.js',
            ),

            /* ── Frontend Template Files ────────── */
            'tpl_overlay'   => array(
                'dir' => $base_dir . 'frontend/templates/overlay.php',
            ),
            'tpl_login'     => array(
                'dir' => $base_dir . 'frontend/templates/login.php',
            ),
            'tpl_register'  => array(
                'dir' => $base_dir . 'frontend/templates/register.php',
            ),
            'tpl_otp'       => array(
                'dir' => $base_dir . 'frontend/templates/otp.php',
            ),
            'tpl_toaster'   => array(
                'dir' => $base_dir . 'frontend/templates/toaster.php',
            ),
            'tpl_forgot_password' => array(
                'dir' => $base_dir . 'frontend/templates/forgot-password.php',
            ),
            'tpl_new_password'    => array(
                'dir' => $base_dir . 'frontend/templates/new-password.php',
            ),
            'tpl_host_request'    => array(
                'dir' => $base_dir . 'frontend/templates/host-request.php',
            ),
            'tpl_email_otp' => array(
                'dir' => $base_dir . 'frontend/templates/email-otp.php',
            ),
            'tpl_email_msg' => array(
                'dir' => $base_dir . 'frontend/templates/email-msg.php',
            ),
            'tpl_email_details' => array(
                'dir' => $base_dir . 'frontend/templates/email-details.php',
            ),

            /* ── Admin CSS/JS Files ─────────────── */
            'admin_global_css' => array(
                'dir' => $base_dir . 'includes/assets/global.css',
                'url' => $base_url . 'includes/assets/global.css',
            ),
            'admin_css'     => array(
                'dir' => $base_dir . 'admin/assets/css/admin.css',
                'url' => $base_url . 'admin/assets/css/admin.css',
            ),
            'admin_js'      => array(
                'dir' => $base_dir . 'admin/assets/js/admin.js',
                'url' => $base_url . 'admin/assets/js/admin.js',
            ),
            'admin_toaster_css' => array(
                'dir' => $base_dir . 'admin/assets/css/admin-toaster.css',
                'url' => $base_url . 'admin/assets/css/admin-toaster.css',
            ),
            'admin_toaster_js'  => array(
                'dir' => $base_dir . 'admin/assets/js/admin-toaster.js',
                'url' => $base_url . 'admin/assets/js/admin-toaster.js',
            ),
            'admin_dashboard_css' => array(
                'dir' => $base_dir . 'admin/assets/css/dashboard.css',
                'url' => $base_url . 'admin/assets/css/dashboard.css',
            ),
            'admin_dashboard_js'  => array(
                'dir' => $base_dir . 'admin/assets/js/dashboard.js',
                'url' => $base_url . 'admin/assets/js/dashboard.js',
            ),
            'admin_host_requests_css' => array(
                'dir' => $base_dir . 'admin/assets/css/host-requests.css',
                'url' => $base_url . 'admin/assets/css/host-requests.css',
            ),
            'admin_host_requests_js'  => array(
                'dir' => $base_dir . 'admin/assets/js/host-requests.js',
                'url' => $base_url . 'admin/assets/js/host-requests.js',
            ),

            /* ── Admin Template Files ───────────── */
            'admin_dashboard' => array(
                'dir' => $base_dir . 'admin/templates/dashboard.php',
            ),
            'admin_database'  => array(
                'dir' => $base_dir . 'admin/templates/database.php',
            ),
            'admin_host_requests' => array(
                'dir' => $base_dir . 'admin/templates/host-requests.php',
            ),

            /* ── Include (PHP Class) Files ──────── */
            'inc_db'        => array(
                'dir' => $base_dir . 'includes/class-authme-db.php',
            ),
            'inc_db_schema' => array(
                'dir' => $base_dir . 'includes/db-schema.php',
            ),
            'inc_auth'      => array(
                'dir' => $base_dir . 'includes/class-authme-auth.php',
            ),
            'inc_otp'       => array(
                'dir' => $base_dir . 'includes/class-authme-otp.php',
            ),
            'inc_host_request' => array(
                'dir' => $base_dir . 'includes/class-authme-host-request.php',
            ),
            'inc_email'     => array(
                'dir' => $base_dir . 'includes/class-authme-email.php',
            ),
            'inc_assets'    => array(
                'dir' => $base_dir . 'includes/assets-loader.php',
            ),
        );
    }

    /* ================================================
     * 🔍 QUICK ACCESSORS
     * ================================================ */

    /**
     * Get the server path (dir) for a specific file key.
     *
     * @param string $key File key from the path registry.
     * @return string|null Absolute file path, or null if not found.
     */
    public static function dir( $key ) {
        $paths = self::get_paths();
        return isset( $paths[ $key ]['dir'] ) ? $paths[ $key ]['dir'] : null;
    }

    /**
     * Get the public URL for a specific file key.
     *
     * @param string $key File key from the path registry.
     * @return string|null Public URL, or null if not found.
     */
    public static function url( $key ) {
        $paths = self::get_paths();
        return isset( $paths[ $key ]['url'] ) ? $paths[ $key ]['url'] : null;
    }

    /**
     * Get the filemtime version for cache-busting.
     *
     * @param string $key File key from the path registry.
     * @return int|false File modification time, or false on failure.
     */
    public static function version( $key ) {
        $dir = self::dir( $key );
        return ( $dir && file_exists( $dir ) ) ? filemtime( $dir ) : AUTHME_VERSION;
    }

    /* ================================================
     * 🎨 ENQUEUE FRONTEND ASSETS
     * ================================================ */

    /**
     * Enqueue all frontend CSS and JS files.
     * Hooked to 'wp_enqueue_scripts'.
     */
    public static function enqueue_frontend() {

        /* ── CSS Files ───────────────────── */
        $css_files = array( 'global', 'overlay', 'login', 'register', 'otp', 'toaster', 'forgot_password', 'new_password', 'host_request' );

        foreach ( $css_files as $name ) {
            $key = 'css_' . $name;
            $dir = self::dir( $key );
            $url = self::url( $key );

            if ( $dir && $url && file_exists( $dir ) ) {
                wp_enqueue_style(
                    'authme-' . $name,
                    $url,
                    array(),
                    self::version( $key )
                );
            }
        }

        /* ── JS Files with Dependencies ──── */
        $js_files = array(
            'global'   => array(),
            'toaster'  => array( 'authme-global' ),
            'overlay'  => array( 'authme-global' ),
            'login'    => array( 'authme-global', 'authme-toaster', 'authme-overlay' ),
            'country_phone_regex' => array( 'authme-global' ),
            'register' => array( 'authme-global', 'authme-toaster', 'authme-overlay', 'authme-country_phone_regex' ),
            'otp'             => array( 'authme-global', 'authme-toaster', 'authme-overlay' ),
            'forgot_password' => array( 'authme-global', 'authme-toaster', 'authme-overlay' ),
            'new_password'    => array( 'authme-global', 'authme-toaster', 'authme-overlay' ),
            'host_request'    => array( 'authme-global', 'authme-toaster', 'authme-country_phone_regex' ),
        );

        foreach ( $js_files as $name => $deps ) {
            $key = 'js_' . $name;
            $dir = self::dir( $key );
            $url = self::url( $key );

            if ( $dir && $url && file_exists( $dir ) ) {
                wp_enqueue_script(
                    'authme-' . $name,
                    $url,
                    $deps,
                    self::version( $key ),
                    true // Load in footer
                );
            }
        }

        /* ── Localize AJAX data ──────────── */
        wp_localize_script( 'authme-global', 'authme_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'authme_nonce' ),
        ) );
    }
}
