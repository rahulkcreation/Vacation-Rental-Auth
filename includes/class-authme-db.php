<?php
/**
 * AuthMe Database Manager
 *
 * Handles creation, verification, and management of the
 * wp_authme_otp_storage custom database table.
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AuthMe_DB {

    /**
     * OTP storage table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Host request table name.
     *
     * @var string
     */
    private $host_table_name;

    /* ──────────────────────────────────────── */

    public function __construct() {
        $this->table_name = AuthMe_DB_Schema::otp_table();
        $this->host_table_name = AuthMe_DB_Schema::host_request_table();
    }

    /* ──────────────────────────────────────── */

    /**
     * Create the tables using dbDelta and the DB Schema registry.
     *
     * @return bool True on success.
     */
    public function create_tables() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        dbDelta( AuthMe_DB_Schema::otp_create_sql() );
        dbDelta( AuthMe_DB_Schema::host_request_create_sql() );

        return true;
    }

    /* ──────────────────────────────────────── */

    /**
     * Check the status of the database tables and their columns.
     *
     * @return array Associative array with table existence and column details.
     */
    public function check_table_status() {
        global $wpdb;

        $status = array(
            'otp_table'  => array('name' => $this->table_name, 'exists' => false, 'columns' => array()),
            'host_table' => array('name' => $this->host_table_name, 'exists' => false, 'columns' => array()),
            'all_good'   => false,
        );

        // Check exists
        $status['otp_table']['exists'] = ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->table_name)) === $this->table_name);
        $status['host_table']['exists'] = ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->host_table_name)) === $this->host_table_name);

        $all_good = true;

        // Check OTP Columns
        if ($status['otp_table']['exists']) {
            $existing_otp_cols = $wpdb->get_col("SHOW COLUMNS FROM {$this->table_name}", 0);
            $required_otp = AuthMe_DB_Schema::otp_required_columns();
            foreach ($required_otp as $col) {
                $exists = in_array($col, $existing_otp_cols, true);
                $status['otp_table']['columns'][$col] = $exists;
                if (!$exists) $all_good = false;
            }
        } else {
            $all_good = false;
        }

        // Check Host Columns
        if ($status['host_table']['exists']) {
            $existing_host_cols = $wpdb->get_col("SHOW COLUMNS FROM {$this->host_table_name}", 0);
            $required_host = AuthMe_DB_Schema::host_required_columns();
            foreach ($required_host as $col) {
                $exists = in_array($col, $existing_host_cols, true);
                $status['host_table']['columns'][$col] = $exists;
                if (!$exists) $all_good = false;
            }
        } else {
            $all_good = false;
        }

        $status['all_good'] = $all_good;

        return $status;
    }

    /* ──────────────────────────────────────── */

    /**
     * Get the table name.
     *
     * @return string
     */
    public function get_table_name() {
        return $this->table_name;
    }
}
