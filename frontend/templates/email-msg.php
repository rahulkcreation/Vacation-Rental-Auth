<?php
/**
 * AuthMe — Message Email Template
 *
 * Used for notification-only emails (no OTP, no credentials):
 *   - Password reset success
 *   - Host request rejection
 *
 * Variables available:
 *   $authme_email_title — Main heading text
 *   $authme_email_desc  — Body description text
 *   $authme_email_note  — Bottom note text (shown in red)
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$site_name = get_bloginfo( 'name' );
$site_url  = esc_url( home_url() );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $authme_email_title ); ?></title>
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
                                <?php echo esc_html( $authme_email_title ); ?>
                            </h1>

                            <!-- Description -->
                            <p style="margin:0 0 32px; color:#5a6e7c; font-size:0.85rem; line-height:1.5;">
                                <?php echo esc_html( $authme_email_desc ); ?>
                            </p>

                            <!-- Note -->
                            <p style="margin:0; color:#ea0124; font-size:0.85rem;">
                                <?php echo esc_html( $authme_email_note ); ?>
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
