<?php
/**
 * AuthMe Admin — View Host Form
 *
 * Decoupled standalone template for viewing a specific host request.
 *
 * @package AuthMe
 */

if (! defined('ABSPATH')) {
    exit;
}

$host_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<div class="wrap">
<div class="amv-view-content-container" id="amv-view-container" data-host-id="<?php echo esc_attr($host_id); ?>">
    <article class="amv-review-card">
        
        <!-- Header -->
        <header class="amv-card-header">
            <div class="amv-header-left">
                <button class="amv-btn-back" onclick="amvGoBack()" aria-label="Go Back">
                    <svg class="amv-icon amv-icon-arrow" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </button>
                <h2 class="amv-card-title">Application Review</h2>
            </div>
        </header>

        <!-- Profile Info -->
        <div class="amv-profile-section" style="display: none;" id="amv-profile-wrap">
            <div class="amv-avatar-circle">
                <span class="amv-avatar-initials" id="amv-view-initials">--</span>
            </div>
            <div class="amv-profile-info">
                <h3 class="amv-profile-name" id="amv-view-name">Loading...</h3>
                <p class="amv-profile-username" id="amv-view-email">Loading...</p>
            </div>
        </div>

        <!-- Details -->
        <div class="amv-details-section" style="display: none;" id="amv-details-wrap">
            <div class="amv-detail-row">
                <span class="amv-detail-label">Application ID</span>
                <span class="amv-detail-value" id="amv-view-id">--</span>
            </div>
            <div class="amv-detail-row">
                <span class="amv-detail-label">Phone</span>
                <span class="amv-detail-value" id="amv-view-phone">--</span>
            </div>
        </div>

        <!-- Documents -->
        <div class="amv-docs-section" style="display: none;" id="amv-docs-wrap">
            <h4 class="amv-section-title">Uploaded Documents</h4>
            <div class="amv-doc-list" id="amv-view-docs-list">
                <!-- Docs will be injected via JS -->
            </div>
        </div>

        <!-- Status -->
        <div class="amv-status-section" style="display: none;" id="amv-status-wrap">
            <h4 class="amv-section-title">Application Status</h4>
            <div class="amv-status-badge-wrapper">
                <span class="amv-status-badge" id="amv-view-status">Loading...</span>
            </div>
        </div>

        <!-- Action / Update Status -->
        <div class="amv-action-section" id="amv-view-actions" style="display: none;">
            <!-- Action buttons injected via JS -->
        </div>

        <div id="amv-loading-state" style="padding: 40px; text-align: center; color: var(--authme-grey-light-text);">
            Loading application data...
        </div>
        <div id="amv-error-state" style="padding: 40px; text-align: center; color: var(--authme-error); display: none;"></div>
        
    </article>
</div>
</div>
