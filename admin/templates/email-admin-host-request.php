<?php
/**
 * AuthMe — Administrator Host Request Notification Template
 *
 * Used to notify the admin when a new host request is submitted.
 *
 * Variables available:
 *   $authme_admin_data — Array containing 'username', 'email', 'mobile'
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name = get_bloginfo( 'name' );
$site_url      = esc_url( home_url() );
$dashboard_url = esc_url( admin_url( 'admin.php?page=authme-host-requests' ) );
$email_title   = 'Host account request';
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
								A new host account application has been received. Please review the details below in the administrator panel.
							</p>

							<!-- Application Details Card -->
							<table width="90%" cellpadding="0" cellspacing="0" style="background-color:#FAFAFA; border-radius:16px; padding:16px 12px; border:1px dotted #e2e8f0; margin:0 auto 32px;">
								<!-- Username Row -->
								<tr>
									<td style="padding:8px 12px; font-size:0.9rem; font-weight:600; color:#000000; text-align:left;">
										Username
									</td>
									<td style="padding:8px 12px; font-size:0.8rem; font-weight:400; color:#5a6e7c; text-align:right;">
										<?php echo esc_html( $authme_admin_data['username'] ); ?>
									</td>
								</tr>
								<!-- Email Row -->
								<tr>
									<td style="padding:8px 12px; font-size:0.9rem; font-weight:600; color:#000000; text-align:left;">
										Email
									</td>
									<td style="padding:8px 12px; font-size:0.8rem; font-weight:400; color:#5a6e7c; text-align:right;">
										<?php echo esc_html( $authme_admin_data['email'] ); ?>
									</td>
								</tr>
								<!-- Mobile Row -->
								<tr>
									<td style="padding:8px 12px; font-size:0.9rem; font-weight:600; color:#000000; text-align:left;">
										Mobile
									</td>
									<td style="padding:8px 12px; font-size:0.8rem; font-weight:400; color:#5a6e7c; text-align:right;">
										<?php echo esc_html( $authme_admin_data['mobile'] ); ?>
									</td>
								</tr>
							</table>

							<!-- CTA Button -->
							<a href="<?php echo $dashboard_url; ?>" style="display:inline-block; background-color:#F15E74; color:#ffffff; padding:14px 28px; border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none; box-shadow:0 4px 12px rgba(241, 94, 116, 0.2);">
								Go to Dashboard
							</a>
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td style="padding:24px 32px; background-color:#ffffff; border-top:1px solid #e9eef3; text-align:center;">
							<p style="margin:0; color:#5a6e7c; font-size:0.8rem;">
								&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
								<a href="<?php echo $site_url; ?>" style="color:#5a6e7c; text-decoration:none; cursor:pointer;">
									<?php echo esc_html( strtoupper( $site_name ) ); ?>
								</a>
								— Administrative Notification
							</p>
						</td>
					</tr>

				</table>
			</td>
		</tr>
	</table>

</body>
</html>
