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
    <div class="amh-content-container" id="amv-view-container" data-host-id="<?php echo esc_attr($host_id); ?>">
        <article class="amh-review-card">

            <!-- Section 1: Review Header -->
            <header class="amh-card-header">
                <div class="amh-header-left">
                    <button class="amh-btn-back" onclick="amvGoBack()" aria-label="Go Back">
                        <svg class="amh-icon amh-icon-arrow" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <line x1="19" y1="12" x2="5" y2="12" class="amh-svg-line"></line>
                            <polyline points="12 19 5 12 12 5" class="amh-svg-polyline"></polyline>
                        </svg>
                    </button>
                    <h2 class="amh-card-title">Application Review</h2>
                </div>
            </header>

            <!-- Loading & Error States -->
            <div id="amv-loading-state" style="padding: 40px; text-align: center; color: var(--authme-grey-light-text);">
                Loading application data...
            </div>
            <div id="amv-error-state" style="padding: 40px; text-align: center; color: var(--authme-error); display: none;"></div>

            <!-- Content Area (Hidden initially) -->
            <div id="amv-content-area" style="display: none; display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Section 2: Profile Info -->
                <div class="amh-profile-section">
                    <div class="amh-avatar-circle">
                        <span class="amh-avatar-initials" id="amv-view-initials">--</span>
                    </div>
                    <div class="amh-profile-info">
                        <h3 class="amh-profile-name" id="amv-view-name">Loading...</h3>
                        <p class="amh-profile-username" id="amv-view-username">Loading...</p>
                    </div>
                </div>

                <!-- Section 3: Contact Details -->
                <div class="amh-details-section">
                    <div class="amh-detail-row">
                        <span class="amh-detail-label">Application ID</span>
                        <span class="amh-detail-value" id="amv-view-id">--</span>
                    </div>

                    <div class="amh-detail-row">
                        <span class="amh-detail-label">Email ID</span>
                        <span class="amh-detail-value" id="amv-view-email-detail">--</span>
                    </div>

                    <div class="amh-detail-row">
                        <span class="amh-detail-label">Phone</span>
                        <span class="amh-detail-value" id="amv-view-phone">--</span>
                    </div>
                </div>

                <!-- Section 4: Uploaded Documents -->
                <div class="amh-docs-section">
                    <h4 class="amh-section-title">Uploaded Documents</h4>
                    <div class="amh-doc-list" id="amv-view-docs-list">
                        <!-- Docs will be injected via JS -->
                    </div>
                </div>

                <!-- Section 5: Application Status -->
                <div class="amh-status-section">
                    <h4 class="amh-section-title">Application Status</h4>
                    <div class="amh-status-badge-wrapper">
                        <span class="amh-status-badge" id="amv-view-status">Loading...</span>
                    </div>
                </div>

                <!-- Section 6: Update Action -->
                <div class="amh-action-section" id="amv-view-actions">
                    <!-- Action buttons injected via JS -->
                </div>

            </div>
            
        </article>
        
        <!-- Document Modal -->
        <div id="amh-doc-modal" class="amh-modal" style="display: none;">
            <div class="amh-modal-overlay" onclick="document.getElementById('amh-doc-modal').style.display='none'"></div>
            <div class="amh-modal-content">
                <button class="amh-modal-close" onclick="document.getElementById('amh-doc-modal').style.display='none'" aria-label="Close Modal">
                    <svg class="amh-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <img id="amh-modal-image" src="" alt="Document Preview" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 8px;">
            </div>
        </div>

    </div>
</div>
