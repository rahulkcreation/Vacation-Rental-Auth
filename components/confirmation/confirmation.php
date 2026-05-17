<?php
/**
 * AuthMe — Global Confirmation Modal Template
 */
?>
<div id="authme-confirm-modal" class="authme-confirm-modal authme-global-plugin-wrapper" style="display: none;">
    <div class="authme-confirm-overlay"></div>
    <div class="authme-confirm-container">
        <div class="authme-confirm-content">
            <div class="authme-confirm-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 class="authme-confirm-title" id="authme-confirm-title">Confirm Action</h3>
            <p class="authme-confirm-message" id="authme-confirm-message">Are you sure you want to proceed?</p>
        </div>
        <div class="authme-confirm-actions">
            <button type="button" class="authme-confirm-btn authme-confirm-btn-cancel" id="authme-confirm-cancel">Cancel</button>
            <button type="button" class="authme-confirm-btn authme-confirm-btn-primary" id="authme-confirm-proceed">Confirm</button>
        </div>
    </div>
</div>
