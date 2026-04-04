<?php

/**
 * AuthMe Admin — Dashboard Page
 *
 * Displays the main plugin dashboard with stats,
 * usage methods, quick links, and security information.
 * Matches the authme-dash.html reference screen exactly.
 *
 * All styling via am-* classes in dashboard.css.
 * All colors via CSS variables from global.css.
 * Zero inline CSS. Zero hardcoded color codes.
 *
 * @package AuthMe
 */

if (! defined('ABSPATH')) {
    exit;
}

/* ── Fetch dynamic data ─────────────────── */
$authme_version    = defined('AUTHME_VERSION') ? AUTHME_VERSION : '1.0.0';
$customer_count    = count(get_users(array('role' => 'traveller')));
$home_url          = home_url();
$authme_url        = home_url('/authme');
$become_host_url   = home_url('/?become-host');
$logout_url        = home_url('/?authme_logout=1');
$db_page_url       = admin_url('admin.php?page=authme-database');
$host_page_url     = admin_url('admin.php?page=authme-host-requests');
?>

<div id="am-dashboard">

    <!-- ================================================ -->
    <!-- Header Section                                    -->
    <!-- ================================================ -->
    <div class="am-flex-between am-header-wrapper">
        <div>
            <h1 class="am-title">AuthMe Dashboard</h1>
            <div class="am-subhead">WordPress Authentication Plugin by <span class="am-text-bold">Art-Tech Fuzion</span> — seamless overlay &amp; host management</div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- Stats Row: Version, Total Customers, Security     -->
    <!-- ================================================ -->
    <div class="am-stats-grid">
        <div class="am-stat-card">
            <div class="am-stat-label">VERSION</div>
            <div class="am-stat-value"><?php echo esc_html($authme_version); ?></div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-label">TOTAL CUSTOMERS</div>
            <div class="am-stat-value"><?php echo esc_html($customer_count); ?></div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-label">SECURITY</div>
            <div class="am-stat-value am-stat-value-otp">🔒 OTP <span class="am-badge am-badge-success">Enabled</span></div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- Intro Description Card                            -->
    <!-- ================================================ -->
    <div class="am-card am-card-intro">
        <p class="am-text am-text-tight">✨ <span class="am-text-bold">AuthMe overlay</span> is automatically injected on every frontend page. Use any method below to open the auth popup or become a host modal.</p>
    </div>

    <!-- ================================================ -->
    <!-- Two Column Layout                                 -->
    <!-- ================================================ -->
    <div class="am-two-columns">

        <!-- LEFT COLUMN: Methods ────────────────────── -->
        <div>

            <!-- Open Authentication Overlay ────────── -->
            <div class="am-card am-card-section">
                <h2 class="am-subtitle">🔐 Open Authentication Overlay</h2>

                <!-- Method 1: Direct URL -->
                <div class="am-method-item">
                    <h3 class="am-heading">1. Direct URL</h3>
                    <p class="am-text">Share this link — when visited, redirects to homepage &amp; auto‑opens the auth popup.</p>
                    <div class="am-code-block">
                        <span class="am-code-text"><?php echo esc_url($authme_url); ?></span>
                        <button class="am-copy-btn" data-copy="<?php echo esc_url($authme_url); ?>">Copy</button>
                    </div>
                </div>

                <!-- Method 2: Trigger Link (HTML) -->
                <div class="am-method-item">
                    <h3 class="am-heading">2. Trigger Link (HTML)</h3>
                    <p class="am-text">Add this HTML anywhere in your theme to create a clickable link that opens the auth popup.</p>
                    <div class="am-code-block">
                        <span class="am-code-text">&lt;a href="#" class="authme-trigger-link"&gt;Login / Register&lt;/a&gt;</span>
                        <button class="am-copy-btn" data-copy='<a href="#" class="authme-trigger-link">Login / Register</a>'>Copy</button>
                    </div>
                </div>

                <!-- Method 3: JavaScript -->
                <div class="am-method-item">
                    <h3 class="am-heading">3. JavaScript</h3>
                    <p class="am-text">Call this function from any <code class="am-inline-code">onclick</code> handler or custom JS to open the popup programmatically.</p>
                    <div class="am-code-block">
                        <code class="am-code-text">authmeOpenOverlay();</code>
                        <button class="am-copy-btn" data-copy="authmeOpenOverlay();">Copy</button>
                    </div>
                </div>
            </div>

            <!-- Become a Host Section ──────────────── -->
            <div class="am-card am-card-section">
                <h2 class="am-subtitle">🏨 Become a Host — Multi‑step Modal</h2>

                <!-- Host Direct URL -->
                <div class="am-method-item">
                    <h3 class="am-heading">1. Become a Host — Direct URL</h3>
                    <p class="am-text">Share this link to automatically load and open the "Become a Host" multi‑step application modal.</p>
                    <div class="am-code-block">
                        <span class="am-code-text"><?php echo esc_url($become_host_url); ?></span>
                        <button class="am-copy-btn" data-copy="<?php echo esc_url($become_host_url); ?>">Copy</button>
                    </div>
                </div>

                <!-- Host JavaScript -->
                <div class="am-method-item">
                    <h3 class="am-heading">2. Become a Host — JavaScript</h3>
                    <p class="am-text">Call this function from any <code class="am-inline-code">onclick</code> or custom JS to open the host request modal programmatically.</p>
                    <div class="am-code-block">
                        <code class="am-code-text">authmeOpenHostModal();</code>
                        <button class="am-copy-btn" data-copy="authmeOpenHostModal();">Copy</button>
                    </div>
                </div>
            </div>

            <!-- Universal Logout & Important Notes ──── -->
            <div class="am-card">
                <h2 class="am-subtitle">🔓 Universal Logout</h2>

                <div class="am-method-item">
                    <h3 class="am-heading">1. Logout — Direct URL</h3>
                    <p class="am-text">Securely log out the current user and redirect back to the homepage. Works natively for any user session.</p>
                    <div class="am-code-block">
                        <span class="am-code-text"><?php echo esc_url($logout_url); ?></span>
                        <button class="am-copy-btn" data-copy="<?php echo esc_url($logout_url); ?>">Copy</button>
                    </div>
                </div>

                <div class="am-note-box">
                    <span class="am-text-bold am-text-warning">⚠️ Important — Permalinks flush required</span><br>
                    <span class="am-text">After activating the plugin, go to <span class="am-text-bold">Settings → Permalinks</span> and click <span class="am-text-bold">Save Changes</span> to flush rewrite rules. This is required for the <code class="am-inline-code">/authme</code> URL to work.</span>
                </div>
                <p class="am-text am-text-small am-text-success">✅ The AuthMe plugin automatically handles session &amp; OTP verification. All frontend endpoints are ready.</p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Quick Links & Security ────── -->
        <div>

            <!-- Quick Links Card ────────────────────── -->
            <div class="am-card am-card-section">
                <h3 class="am-heading">📌 Quick Links</h3>
                <div class="am-quick-link">
                    <span class="am-icon">🗄️</span>
                    <a href="<?php echo esc_url($db_page_url); ?>" class="am-link-accent">Database Management</a>
                    <span class="am-quick-link-arrow">→</span>
                </div>
                <div class="am-quick-link">
                    <span class="am-icon">📋</span>
                    <a href="<?php echo esc_url($host_page_url); ?>" class="am-link-accent">Host Requests</a>
                    <span class="am-quick-link-arrow">→</span>
                </div>
                <div class="am-quick-link">
                    <span class="am-icon">⚙️</span>
                    <span class="am-text-secondary">Plugin Settings</span>
                    <span class="am-quick-link-arrow">/wp-admin</span>
                </div>
                <div class="am-quick-link">
                    <span class="am-icon">🔐</span>
                    <span class="am-text-secondary">Security Audit (OTP logs)</span>
                </div>
            </div>

            <!-- Security Status Card ────────────────── -->
            <div class="am-card">
                <h3 class="am-heading">🛡️ Security Status</h3>
                <div class="am-security-badges">
                    <div class="am-badge am-badge-otp">OTP Two-Factor</div>
                    <div class="am-badge am-badge-session">Session Encryption</div>
                </div>
                <p class="am-text">AuthMe ensures that every frontend authentication flow is protected by OTP verification and secure tokens.</p>
                <hr class="am-divider">
                <div class="am-flex-between">
                    <span class="am-text-medium">Plugin Version:</span>
                    <span class="am-text-secondary"><?php echo esc_html($authme_version); ?></span>
                </div>
                <div class="am-flex-between">
                    <span class="am-text-medium">Compatibility:</span>
                    <span class="am-text-secondary">WordPress 6.0+</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- Footer                                            -->
    <!-- ================================================ -->
    <footer class="am-footer">
        AuthMe WordPress Authentication Plugin by Art-Tech Fuzion — seamless auth, host management &amp; OTP security.<br>
        Use <code class="am-inline-code">/authme</code> endpoint, trigger links, or JS methods to integrate.
    </footer>

</div>