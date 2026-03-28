<?php
/**
 * AuthMe Host Request Handler
 *
 * Handles AJAX requests for the "Become a Host" feature,
 * including real-time validation and final submission.
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AuthMe_Host_Request {

    /**
     * Database table name.
     *
     * @var string
     */
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'host_request';
    }

    /* ──────────────────────────────────────── */

    /**
     * AJAX handler: Check if username is available.
     * Reuses validation logic similar to regular registration.
     */
    public function ajax_check_host_username() {
        check_ajax_referer( 'authme_nonce', 'nonce' );

        $username = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';

        if ( empty( $username ) ) {
            wp_send_json_error( array( 'message' => 'Username is required.' ) );
        }

        if ( ! preg_match( '/^[a-zA-Z][a-zA-Z0-9]{3,13}$/', $username ) ) {
            wp_send_json_error( array( 'message' => 'Username must be 4–14 alphanumeric characters and start with a letter.' ) );
        }

        if ( username_exists( $username ) ) {
            wp_send_json_error( array( 'message' => 'Username is not available.' ) );
        }

        wp_send_json_success( array( 'message' => 'Username available.' ) );
    }

    /* ──────────────────────────────────────── */

    /**
     * AJAX handler: Check if email is available.
     */
    public function ajax_check_host_email() {
        check_ajax_referer( 'authme_nonce', 'nonce' );

        $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => 'Valid email is required.' ) );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( array( 'message' => 'Email already exists.' ) );
        }

        wp_send_json_success( array( 'message' => 'Email available.' ) );
    }

    /* ──────────────────────────────────────── */

    /**
     * AJAX handler: Check if mobile number is available.
     */
    public function ajax_check_host_mobile() {
        check_ajax_referer( 'authme_nonce', 'nonce' );

        $mobile = isset( $_POST['mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['mobile'] ) ) : '';

        if ( empty( $mobile ) ) {
            wp_send_json_error( array( 'message' => 'Mobile number is required.' ) );
        }

        global $wpdb;
        // Check if this mobile number already exists in wp_usermeta
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'mobile_number' AND meta_value = %s LIMIT 1",
            $mobile
        ) );

        if ( $exists ) {
            wp_send_json_error( array( 'message' => 'Mobile number is already registered.' ) );
        }

        wp_send_json_success( array( 'message' => 'Mobile number available.' ) );
    }

    /* ──────────────────────────────────────── */

    /**
     * AJAX handler: Submit final host request.
     * Expects a JSON string of user_data containing all info and base64 images.
     */
    public function ajax_submit_host_request() {
        check_ajax_referer( 'authme_nonce', 'nonce' );

        // Extract raw JSON data
        $user_data = isset( $_POST['user_data'] ) ? wp_unslash( $_POST['user_data'] ) : '';

        if ( empty( $user_data ) ) {
            wp_send_json_error( array( 'message' => 'No data provided.' ) );
        }

        // Validate that it's actually valid JSON by decoding it
        $decoded = json_decode( $user_data, true );
        if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => 'Invalid data format.' ) );
        }

        // Perform basic validations on the extracted data
        $username = isset( $decoded['username'] ) ? sanitize_user( $decoded['username'] ) : '';
        $email    = isset( $decoded['email'] ) ? sanitize_email( $decoded['email'] ) : '';

        if ( empty( $username ) || username_exists( $username ) ) {
            wp_send_json_error( array( 'message' => 'Username is invalid or already taken.' ) );
        }

        if ( empty( $email ) || ! is_email( $email ) || email_exists( $email ) ) {
            wp_send_json_error( array( 'message' => 'Email is invalid or already exists.' ) );
        }

        // Insert into host_request table
        global $wpdb;
        $inserted = $wpdb->insert(
            $this->table_name,
            array(
                'user_data' => $user_data, // Storing the raw JSON payload
                'status'    => 'pending',
                'date'      => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s' )
        );

        if ( ! $inserted ) {
            wp_send_json_error( array( 'message' => 'Failed to submit application. Please try again.' ) );
        }

        wp_send_json_success( array( 'message' => 'Application submitted successfully.' ) );
    }

}
