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

    /**
     * Required columns in the OTP storage table.
     *
     * @var array
     */
    private $required_columns = array(
        'id', 'email', 'otp_code', 'purpose',
        'created_at', 'expires_at', 'is_verified', 'user_data',
    );

    /* ──────────────────────────────────────── */

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'authme_otp_storage';
        $this->host_table_name = $wpdb->prefix . 'host_request';
    }

    /* ──────────────────────────────────────── */

    /**
     * Create the OTP storage table using dbDelta.
     *
     * @return bool True on success.
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            email VARCHAR(100) NOT NULL,
            otp_code VARCHAR(6) NOT NULL,
            purpose VARCHAR(20) NOT NULL DEFAULT 'registration',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            user_data TEXT,
            PRIMARY KEY (id),
            KEY email_purpose (email, purpose)
        ) $charset_collate;";

        $sql_host = "CREATE TABLE {$this->host_table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_data LONGTEXT,
            status VARCHAR(50) NOT NULL DEFAULT 'pending',
            date DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        dbDelta( $sql_host );

        return true;
    }

    /* ──────────────────────────────────────── */

    /**
     * Check the status of the OTP storage table and its columns.
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
            foreach ($this->required_columns as $col) {
                $exists = in_array($col, $existing_otp_cols, true);
                $status['otp_table']['columns'][$col] = $exists;
                if (!$exists) $all_good = false;
            }
        } else {
            $all_good = false;
        }

        // Check Host Columns
        $host_required_cols = array('id', 'user_data', 'status', 'date');
        if ($status['host_table']['exists']) {
            $existing_host_cols = $wpdb->get_col("SHOW COLUMNS FROM {$this->host_table_name}", 0);
            foreach ($host_required_cols as $col) {
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
