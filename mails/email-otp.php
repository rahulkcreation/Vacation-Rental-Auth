<?php
/**
 * AuthMe — Unified OTP Email Template
 *
 * Used for all OTP-based verifications:
 * registration, password_reset, host_request.
 *
 * Variables available:
 *   $authme_otp_code    — 6-digit OTP code
 *   $authme_otp_purpose — 'registration' | 'password_reset' | 'host_request'
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Derive dynamic content from purpose ── */
$site_name = get_bloginfo( 'name' );
$site_url  = esc_url( home_url() );

if ( $authme_otp_purpose === 'password_reset' ) {
    $email_title = 'Verification Code';
    $email_desc  = 'Use the following one-time password to complete your reset password. This code is valid for <strong>60 seconds</strong>.';
    $email_note  = 'Note: This code is requested to reset your password, If you have not requested kindly change your password to secure.';
} elseif ( $authme_otp_purpose === 'host_request' ) {
    $email_title = 'Verification Code';
    $email_desc  = 'Use the following one-time password to complete your registration. This code is valid for <strong>60 seconds</strong>.';
    $email_note  = 'If you did not request this code, you can safely ignore this email.';
} else {
    /* Default: registration */
    $email_title = 'Registration Verification Code';
    $email_desc  = 'Use the following one-time password to complete your registration. This code is valid for <strong>60 seconds</strong>.';
    $email_note  = 'If you did not request this code, you can safely ignore this email.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $email_title ); ?></title>
</head>
<body style="margin:0; padding:0; background-color:#FAFAFA; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAFAFA; padding:20px 0;">
        <tr>
            <td align="center">
                <table width="500" cellpadding="0" cellspacing="0" style="max-width:500px; width:96%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#F15E74; padding:28px 20px; text-align:center;">
                            <h2 style="margin:0; color:#ffffff; font-size:1.2rem; font-weight:700; letter-spacing:0.5px;">
                                <?php echo esc_html( strtoupper( $site_name ) ); ?>
                            </h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px 14px; text-align:center;">
                            <!-- Title -->
                            <h1 style="margin:0 0 16px; color:#000000; font-size:1.2rem; font-weight:700;">
                                <?php echo esc_html( $email_title ); ?>
                            </h1>

                            <!-- Description -->
                            <p style="margin:0 0 32px; color:#5a6e7c; font-size:0.85rem; line-height:1.5;">
                                <?php echo wp_kses( $email_desc, array( 'strong' => array() ) ); ?>
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color:#FAFAFA; border:2px dashed #e2e8f0; border-radius:12px; padding:20px; margin:0 auto 32px; max-width:320px;">
                                <span style="color:#000000; font-size:36px; font-weight:700; letter-spacing:14px; margin-left:14px; display:inline-block;">
                                    <?php echo esc_html( $authme_otp_code ); ?>
                                </span>
                            </div>

                            <!-- Note -->
                            <p style="margin:0; color:#5a6e7c; font-size:0.85rem;">
                                <?php echo esc_html( $email_note ); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:24px 32px; background-color:#ffffff; border-top:1px solid #e9eef3; text-align:center;">
                            <p style="margin:0; color:#5a6e7c; font-size:0.8rem;">
                                &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
                                <a href="<?php echo $site_url; ?>" style="color:#5a6e7c; text-decoration:none;">
                                    <?php echo esc_html( strtoupper( $site_name ) ); ?>
                                </a>
                                — Powered by AuthMe
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
