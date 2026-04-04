<?php
/**
 * AuthMe Email Handler
 *
 * Sends all email notifications using WordPress wp_mail().
 * Uses three template files:
 *   email-otp.php     — OTP verification emails
 *   email-msg.php     — Message-only notifications
 *   email-details.php — Credential-based notifications
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AuthMe_Email {

    /**
     * Send an OTP email to the user.
     *
     * Uses email-otp.php template which dynamically adjusts
     * content based on the purpose parameter.
     *
     * @param string $to_email  Recipient email address.
     * @param string $otp_code  The 6-digit OTP code.
     * @param string $purpose   'registration' | 'password_reset' | 'host_request'
     * @return bool             True if mail was sent successfully.
     */
    public function send_otp_email( $to_email, $otp_code, $purpose = 'registration' ) {

        $site_name = get_bloginfo( 'name' );

        /* Build the subject line based on purpose */
        if ( $purpose === 'password_reset' ) {
            $subject = 'Reset Password Verification code — ' . $site_name;
        } elseif ( $purpose === 'host_request' ) {
            $subject = 'Host Id Verification Code — ' . $site_name;
        } else {
            $subject = 'Your Registration Verification Code — ' . $site_name;
        }

        /* Build the HTML email body from template */
        ob_start();
        $authme_otp_code    = $otp_code;
        $authme_otp_purpose = $purpose;
        include AUTHME_PLUGIN_DIR . 'frontend/templates/email-otp.php';
        $body = ob_get_clean();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        return wp_mail( $to_email, $subject, $body, $headers );
    }

    /* ──────────────────────────────────────── */

    /**
     * Send a "password changed" notification email.
     *
     * Uses email-msg.php template with password-change content.
     *
     * @param string $to_email    Recipient email address.
     * @param string $user_name   User's display name (unused now but kept for API compat).
     * @return bool               True if mail was sent successfully.
     */
    public function send_password_changed_email( $to_email, $user_name ) {

        $site_name   = get_bloginfo( 'name' );
        $admin_email = get_option( 'admin_email' );
        $subject     = 'Reset Password Successfully — ' . $site_name;

        ob_start();
        $authme_email_title = 'Account password changed';
        $authme_email_desc  = 'Your account password is changed successfully. Now your requested password is saved to database, you can login with your new password.';
        $authme_email_note  = 'Note: If you did not changed your password then reset your password now or contact to admin on this email - (' . $admin_email . ').';
        include AUTHME_PLUGIN_DIR . 'frontend/templates/email-msg.php';
        $body = ob_get_clean();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        return wp_mail( $to_email, $subject, $body, $headers );
    }

    /* ──────────────────────────────────────── */

    /**
     * Send email when a Host Account is approved.
     *
     * Uses email-details.php template which shows login credentials.
     *
     * @param string $to_email  Recipient email address.
     * @param string $username  Generated username for the host.
     * @param string $password  Generated password for the host.
     * @return bool
     */
    public function send_host_approved_email( $to_email, $username, $password ) {

        $site_name = get_bloginfo( 'name' );
        $subject   = 'Congratulations!! Your Host ID is Approved — ' . $site_name;

        ob_start();
        $authme_email_title    = 'Application Approved';
        $authme_email_desc     = 'Congratulations, your host account is approved! Now you can list your property. Here are your securely generated login credentials:';
        $authme_host_username  = $username;
        $authme_host_password  = $password;
        $authme_email_note     = 'Note: This is system generated password. Please securely login and change this auto-generated password immediately!';
        include AUTHME_PLUGIN_DIR . 'frontend/templates/email-details.php';
        $body = ob_get_clean();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        return wp_mail( $to_email, $subject, $body, $headers );
    }

    /* ──────────────────────────────────────── */

    /**
     * Send email when a Host Account is rejected.
     *
     * Uses email-msg.php template with rejection content.
     *
     * @param string $to_email  Recipient email address.
     * @return bool
     */
    public function send_host_rejected_email( $to_email ) {

        $site_name   = get_bloginfo( 'name' );
        $admin_email = get_option( 'admin_email' );
        $subject     = 'Regret!! Your host id unverified — ' . $site_name;

        ob_start();
        $authme_email_title = 'Application Rejected';
        $authme_email_desc  = 'We are unable to verified you email because of some document is fake or missing.';
        $authme_email_note  = 'Note: This is auto-generated mail, If you have any query Contact to admin on this email - (' . $admin_email . ').';
        include AUTHME_PLUGIN_DIR . 'frontend/templates/email-msg.php';
        $body = ob_get_clean();

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        return wp_mail( $to_email, $subject, $body, $headers );
    }
}
