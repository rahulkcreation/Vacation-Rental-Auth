<?php
/**
 * AuthMe Admin — Host Requests Page
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'host_request';

// Check if table exists
$table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;

$requests = array();
if ( $table_exists ) {
    // Basic pagination (optional) or just list all recent for now
    $requests = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY date DESC LIMIT 100" );
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Host Requests</h1>
    <hr class="wp-header-end">

    <?php if ( ! $table_exists ) : ?>
        <div class="notice notice-error inline"><p>Error: The <code><?php echo esc_html($table_name); ?></code> table does not exist. Please go to <strong>AuthMe &rarr; Database</strong> and click "Create / Update Tables".</p></div>
    <?php else : ?>
        
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th scope="col" id="id" class="manage-column column-id" style="width: 60px;">ID</th>
                    <th scope="col" id="applicant" class="manage-column column-primary">Applicant Info</th>
                    <th scope="col" id="status" class="manage-column column-status" style="width: 120px;">Status</th>
                    <th scope="col" id="date" class="manage-column column-date" style="width: 180px;">Date Submitted</th>
                    <th scope="col" id="actions" class="manage-column column-actions" style="width: 150px;">Actions</th>
                </tr>
            </thead>

            <tbody id="the-list">
                <?php if ( empty( $requests ) ) : ?>
                    <tr class="no-items"><td class="colspanchange" colspan="5">No host requests found.</td></tr>
                <?php else : ?>
                    <?php foreach ( $requests as $req ) : ?>
                        <?php 
                        $user_data = json_decode( $req->user_data, true ); 
                        $username = isset($user_data['username']) ? esc_html($user_data['username']) : 'N/A';
                        $fullname = isset($user_data['fullname']) ? esc_html($user_data['fullname']) : 'N/A';
                        $email = isset($user_data['email']) ? esc_html($user_data['email']) : 'N/A';
                        $mobile = isset($user_data['mobile']) ? esc_html($user_data['mobile']) : 'N/A';

                        // Check for images
                        $has_aadharf = !empty($user_data['documents']['aadharf']);
                        $has_aadharb = !empty($user_data['documents']['aadharb']);
                        $has_pan = !empty($user_data['documents']['pan']);
                        ?>
                        <tr>
                            <td class="id column-id"><?php echo esc_html( $req->id ); ?></td>
                            
                            <td class="title column-title has-row-actions column-primary" data-colname="Applicant Info">
                                <strong><?php echo $fullname; ?></strong> (<?php echo $username; ?>)<br>
                                <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br>
                                <?php echo $mobile; ?>
                                
                                <div style="margin-top:8px; font-size:12px; color:#666;">
                                    <strong>Docs Attached:</strong> 
                                    <?php echo $has_aadharf ? 'Aadhar Front, ' : ''; ?>
                                    <?php echo $has_aadharb ? 'Aadhar Back, ' : ''; ?>
                                    <?php echo $has_pan ? 'PAN' : ''; ?>
                                </div>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
                            </td>
                            
                            <td class="status column-status" data-colname="Status">
                                <?php 
                                    if ( strtolower($req->status) === 'pending' ) {
                                        echo '<span class="authme-badge" style="background:#fffbeb; color:#f59e0b; padding:4px 8px; border-radius:4px; font-weight:600;">Pending</span>';
                                    } elseif ( strtolower($req->status) === 'approved' ) {
                                        echo '<span class="authme-badge" style="background:#f0fdf4; color:#16a34a; padding:4px 8px; border-radius:4px; font-weight:600;">Approved</span>';
                                    } elseif ( strtolower($req->status) === 'rejected' ) {
                                        echo '<span class="authme-badge" style="background:#fef2f2; color:#dc2626; padding:4px 8px; border-radius:4px; font-weight:600;">Rejected</span>';
                                    } else {
                                        echo '<span class="authme-badge" style="background:#f1f5f9; color:#475569; padding:4px 8px; border-radius:4px; font-weight:600;">' . esc_html( ucfirst($req->status) ) . '</span>';
                                    }
                                ?>
                            </td>
                            
                            <td class="date column-date" data-colname="Date Submitted">
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $req->date ) ) ); ?>
                            </td>
                            
                            <td class="actions column-actions" data-colname="Actions">
                                <!-- Very basic action to quickly view the parsed data block -->
                                <button type="button" class="button" onclick="alert('Viewing functionality can be built out further. Currently showing ID: <?php echo $req->id; ?>.')">View Forms</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>
</div>
