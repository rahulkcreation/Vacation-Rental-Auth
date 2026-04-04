<?php
/**
 * AuthMe Admin Panel
 *
 * Registers admin menu pages, enqueues admin assets,
 * and provides AJAX handlers for database management.
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AuthMe_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Admin AJAX endpoints
        add_action( 'wp_ajax_authme_admin_check_db', array( $this, 'ajax_check_db_status' ) );
        add_action( 'wp_ajax_authme_admin_create_tables', array( $this, 'ajax_create_tables' ) );
        
        // Host Requests Admin AJAX endpoints
        add_action( 'wp_ajax_authme_admin_get_host_requests', array( $this, 'ajax_get_host_requests' ) );
        add_action( 'wp_ajax_authme_admin_get_single_host', array( $this, 'ajax_get_single_host_request' ) );
        add_action( 'wp_ajax_authme_admin_process_host', array( $this, 'ajax_process_host_request' ) );
    }

    /* ──────────────────────────────────────── */

    /**
     * Register admin menu and sub-menu pages.
     */
    public function register_admin_menus() {
        // Main menu: AuthMe
        add_menu_page(
            'AuthMe',                          // Page title
            'AuthMe',                          // Menu title
            'manage_options',                   // Capability
            'authme',                           // Menu slug
            array( $this, 'render_dashboard' ), // Callback
            'dashicons-lock',                   // Icon
            80                                  // Position
        );

        // Sub-menu: Dashboard (overrides the auto-generated first submenu "AuthMe")
        add_submenu_page(
            'authme',                           // Parent slug
            'AuthMe Dashboard',                 // Page title
            'Dashboard',                        // Menu title
            'manage_options',                   // Capability
            'authme',                           // Same slug as parent to override
            array( $this, 'render_dashboard' )  // Callback
        );

        // Sub-menu: Database
        add_submenu_page(
            'authme',                           // Parent slug
            'AuthMe Database',                  // Page title
            'Database',                         // Menu title
            'manage_options',                   // Capability
            'authme-database',                  // Menu slug
            array( $this, 'render_database' )   // Callback
        );

        // Sub-menu: Host Requests
        global $wpdb;
        $host_table = $wpdb->prefix . 'host_request';
        $pending_count = 0;
        if ( $wpdb->get_var("SHOW TABLES LIKE '{$host_table}'") === $host_table ) {
            $pending_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$host_table} WHERE status = 'pending'" );
        }
        
        $host_menu_title = 'Host Requests';
        if ( $pending_count > 0 ) {
            $host_menu_title .= ' <span class="update-plugins count-' . esc_attr( $pending_count ) . '"><span class="plugin-count">' . esc_html( $pending_count ) . '</span></span>';
        }

        add_submenu_page(
            'authme',                           // Parent slug
            'Host Requests',                    // Page title
            $host_menu_title,                   // Menu title
            'manage_options',                   // Capability
            'authme-host-requests',             // Menu slug
            array( $this, 'render_host_requests' ) // Callback
        );

        // Hidden Sub-menu: View Form
        add_submenu_page(
            null,                               // Parent slug null to hide it from menu
            'View Form',                        // Page title
            '',                                 // Menu title
            'manage_options',                   // Capability
            'authme-view-form',                 // Menu slug
            array( $this, 'render_view_form' )  // Callback
        );
    }

    /* ──────────────────────────────────────── */

    /**
     * Enqueue admin CSS and JS only on AuthMe admin pages.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on AuthMe pages
        if ( strpos( $hook, 'authme' ) === false ) {
            return;
        }

        $global_css_file = AUTHME_PLUGIN_DIR . 'includes/assets/global.css';
        $css_file = AUTHME_PLUGIN_DIR . 'admin/assets/css/admin.css';
        $js_file  = AUTHME_PLUGIN_DIR . 'admin/assets/js/admin.js';

        if ( file_exists( $global_css_file ) ) {
            wp_enqueue_style(
                'authme-admin-global-css',
                AUTHME_PLUGIN_URL . 'includes/assets/global.css',
                array(),
                filemtime( $global_css_file )
            );
        }

        $toaster_css = AUTHME_PLUGIN_DIR . 'admin/assets/css/admin-toaster.css';
        $toaster_js  = AUTHME_PLUGIN_DIR . 'admin/assets/js/admin-toaster.js';

        if ( file_exists( $toaster_css ) ) {
            wp_enqueue_style(
                'authme-admin-toaster-css',
                AUTHME_PLUGIN_URL . 'admin/assets/css/admin-toaster.css',
                array( 'authme-admin-global-css' ),
                filemtime( $toaster_css )
            );
        }

        if ( file_exists( $toaster_js ) ) {
            wp_enqueue_script(
                'authme-admin-toaster-js',
                AUTHME_PLUGIN_URL . 'admin/assets/js/admin-toaster.js',
                array(),
                filemtime( $toaster_js ),
                true
            );

            wp_localize_script( 'authme-admin-toaster-js', 'authme_admin', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'authme_admin_nonce' ),
            ) );
        }

        // Dynamically load page-specific CSS/JS if they exist (based on menu slug)
        // e.g., toplevel_page_authme -> dashboard, authme_page_authme-database -> database
        $page_slug = str_replace( array('toplevel_page_authme', 'authme_page_authme-', 'admin_page_authme-'), array('dashboard', '', ''), $hook );
        if ( !empty($page_slug) && $page_slug !== $hook ) {
            $page_css_file = AUTHME_PLUGIN_DIR . 'admin/assets/css/' . $page_slug . '.css';
            $page_js_file  = AUTHME_PLUGIN_DIR . 'admin/assets/js/' . $page_slug . '.js';

            if ( file_exists( $page_css_file ) ) {
                wp_enqueue_style(
                    'authme-' . $page_slug . '-css',
                    AUTHME_PLUGIN_URL . 'admin/assets/css/' . $page_slug . '.css',
                    array( 'authme-admin-global-css' ),
                    filemtime( $page_css_file )
                );
            }
            if ( file_exists( $page_js_file ) ) {
                wp_enqueue_script(
                    'authme-' . $page_slug . '-js',
                    AUTHME_PLUGIN_URL . 'admin/assets/js/' . $page_slug . '.js',
                    array( 'jquery', 'authme-admin-toaster-js' ),
                    filemtime( $page_js_file ),
                    true
                );
            }
        }

        // Hook to render toaster at the bottom of the page
        add_action( 'admin_footer', array( $this, 'render_admin_toaster' ) );
    }

    /**
     * Render the global admin toaster component
     */
    public function render_admin_toaster() {
        // Ensure this only runs on AuthMe pages
        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'authme' ) === false ) {
            return;
        }

        $toaster_file = AUTHME_PLUGIN_DIR . 'admin/templates/admin-toaster.php';
        if ( file_exists( $toaster_file ) ) {
            include $toaster_file;
        }
    }

    /* ──────────────────────────────────────── */

    /**
     * Render the Dashboard page.
     */
    public function render_dashboard() {
        include AUTHME_PLUGIN_DIR . 'admin/templates/dashboard.php';
    }

    /**
     * Render the Database management page.
     */
    public function render_database() {
        include AUTHME_PLUGIN_DIR . 'admin/templates/database.php';
    }

    /**
     * Render the Host Requests page.
     */
    public function render_host_requests() {
        include AUTHME_PLUGIN_DIR . 'admin/templates/host-requests.php';
    }

    /**
     * Render the View Form page.
     */
    public function render_view_form() {
        include AUTHME_PLUGIN_DIR . 'admin/templates/view-form.php';
    }

    /* ──────────────────────────────────────── */

    /**
     * AJAX: Check database table status.
     */
    public function ajax_check_db_status() {
        check_ajax_referer( 'authme_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $db     = new AuthMe_DB();
        $status = $db->check_table_status();

        wp_send_json_success( $status );
    }

    /**
     * AJAX: Create or update database tables.
     */
    public function ajax_create_tables() {
        check_ajax_referer( 'authme_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $db = new AuthMe_DB();

        // Check current status before creating
        $status_before = $db->check_table_status();

        if ( $status_before['all_good'] ) {
            wp_send_json_success( array(
                'message' => 'All tables already exist with the correct structure.',
                'status'  => 'already_created',
            ) );
        }

        // Create / update tables
        $db->create_tables();

        // Verify after creation
        $status_after = $db->check_table_status();

        if ( $status_after['all_good'] ) {
            if ( ! $status_before['otp_table']['exists'] || ! $status_before['host_table']['exists'] ) {
                $msg = 'Create tables successfully';
            } else {
                $msg = 'Updated successfully';
            }

            wp_send_json_success( array(
                'message' => $msg,
                'status'  => 'created',
            ) );
        }

        wp_send_json_error( array( 'message' => 'Failed to create tables. Please try again.' ) );
    }

    /* ── Host Requests AJAX Endpoints ────────── */

    /**
     * Fetch list of host requests for the datatable.
     */
    public function ajax_get_host_requests() {
        check_ajax_referer( 'authme_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'host_request';

        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'all';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
        $page   = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
        $limit  = 10;
        $offset = ( $page - 1 ) * $limit;

        $where_clauses = array();
        $query_args    = array();

        if ( $status !== 'all' ) {
            $where_clauses[] = "status = %s";
            $query_args[]    = $status;
        }

        if ( ! empty( $search ) && strlen( $search ) >= 3 ) {
            $search_like = '%' . $wpdb->esc_like( $search ) . '%';
            $where_clauses[] = "(user_data LIKE %s OR id LIKE %s)";
            $query_args[]    = $search_like;
            $query_args[]    = $search_like;
        }

        $where_sql = '';
        if ( ! empty( $where_clauses ) ) {
            $where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
        }

        // Total counts for tabs (ignoring search)
        $count_pending  = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'pending'" );
        $count_approved = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'approved'" );
        $count_rejected = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'" );
        $count_all      = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

        // Total items matching current filter/search
        $total_query_sql = "SELECT COUNT(*) FROM $table_name $where_sql";
        $total_items = empty( $query_args ) ? $wpdb->get_var( $total_query_sql ) : $wpdb->get_var( $wpdb->prepare( $total_query_sql, $query_args ) );

        // Fetch data
        $data_query_sql = "SELECT id, status, date, user_data FROM $table_name $where_sql ORDER BY date DESC LIMIT %d OFFSET %d";
        $data_args   = array_merge( $query_args, array( $limit, $offset ) );
        $results     = $wpdb->get_results( $wpdb->prepare( $data_query_sql, $data_args ) );

        $formatted_data = array();
        if ( $results ) {
            foreach ( $results as $row ) {
                $user_data = json_decode( $row->user_data, true );
                $formatted_data[] = array(
                    'id'     => $row->id,
                    'raw_id' => $row->id,
                    'email'  => isset( $user_data['email'] ) ? $user_data['email'] : 'N/A',
                    'phone'  => isset( $user_data['mobile'] ) ? $user_data['mobile'] : 'N/A',
                    'fullname'=> isset( $user_data['fullname'] ) ? $user_data['fullname'] : 'N/A',
                    'status' => $row->status,
                    'date'   => wp_date( 'F j, Y g:i A', strtotime( $row->date ) ),
                );
            }
        }

        wp_send_json_success( array(
            'items' => $formatted_data,
            'total' => $total_items,
            'pages' => ceil( $total_items / $limit ),
            'counts' => array(
                'all'      => $count_all,
                'pending'  => $count_pending,
                'approved' => $count_approved,
                'rejected' => $count_rejected,
            )
        ) );
    }

    /**
     * Fetch a single host request.
     */
    public function ajax_get_single_host_request() {
        check_ajax_referer( 'authme_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid ID.' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'host_request';
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

        if ( ! $row ) {
            wp_send_json_error( array( 'message' => 'Request not found.' ) );
        }

        $user_data = json_decode( $row->user_data, true );

        wp_send_json_success( array(
            'id'       => $row->id,
            'raw_id'   => $row->id,
            'status'   => $row->status,
            'userData' => $user_data,
        ) );
    }

    /**
     * Process host request (Approve, Reject, Pending).
     */
    public function ajax_process_host_request() {
        check_ajax_referer( 'authme_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $new_status = isset( $_POST['new_status'] ) ? sanitize_text_field( $_POST['new_status'] ) : '';

        if ( ! $id || ! in_array( $new_status, array( 'pending', 'approved', 'rejected' ) ) ) {
            wp_send_json_error( array( 'message' => 'Invalid data.' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'host_request';
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

        if ( ! $row ) {
            wp_send_json_error( array( 'message' => 'Request not found.' ) );
        }

        // If it's already approved, blocking further action to avoid double user creation.
        if ( $row->status === 'approved' ) {
            wp_send_json_error( array( 'message' => 'This request is already approved.' ) );
        }

        $user_data = json_decode( $row->user_data, true );
        $email = $user_data['email'];
        $username = $user_data['username'];
        $mobile = isset( $user_data['mobile'] ) ? $user_data['mobile'] : '';

        if ( $new_status === 'approved' ) {
            // Check if user exists
            if ( email_exists( $email ) || username_exists( $username ) ) {
                wp_send_json_error( array( 'message' => 'A user with this email or username already exists.' ) );
            }

            // Create user
            $password = wp_generate_password( 12, false );
            $user_id = wp_create_user( $username, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
            }

            // Set Role and Meta
            $user = new WP_User( $user_id );
            $user->set_role( 'host' );

            wp_update_user( array(
                'ID' => $user_id,
                'display_name' => $username,
                'user_nicename' => $username,
            ) );

            update_user_meta( $user_id, 'mobile_number', $mobile );
            update_user_meta( $user_id, 'is_email_verified', '1' );
            update_user_meta( $user_id, 'is_number_verified', '0' );

            // Send Email
            $email_handler = new AuthMe_Email();
            if ( method_exists( $email_handler, 'send_host_approved_email' ) ) {
                $email_handler->send_host_approved_email( $email, $username, $password );
            }
            
            $wpdb->update( $table_name, array( 'status' => 'approved' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
            wp_send_json_success( array( 'message' => 'Request Approved & User Created successfully.' ) );

        } elseif ( $new_status === 'rejected' ) {
            $wpdb->update( $table_name, array( 'status' => 'rejected' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
            
            // Send Email
            $email_handler = new AuthMe_Email();
            if ( method_exists( $email_handler, 'send_host_rejected_email' ) ) {
                $email_handler->send_host_rejected_email( $email );
            }

            wp_send_json_success( array( 'message' => 'Request Rejected successfully.' ) );
        } else {
            // Pending
            $wpdb->update( $table_name, array( 'status' => 'pending' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
            wp_send_json_success( array( 'message' => 'Status set to Pending.' ) );
        }
    }
}

// Initialize admin
new AuthMe_Admin();
