<?php
/**
 * AuthMe Admin — View/Edit User Page
 *
 * @package AuthMe
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h2 class="authme-admin-notice-placeholder"></h2>

    <div class="authme-global-plugin-wrapper">
        <div class="authme-user-v-main-container">
            <div class="authme-user-v-header">
                <div class="authme-user-v-header-left">
                    <a href="<?php echo admin_url('admin.php?page=authme-users'); ?>" class="authme-user-v-back-btn">
                       close
                    </a>
                    <h1 class="authme-user-v-title">Edit Traveler Profile</h1>
                </div>
            </div>

            <div class="authme-user-v-content-card" id="authme-user-v-card">
                <!-- Loading State -->
                <div class="authme-user-v-loading">
                    <div class="authme-user-v-spinner"></div>
                    <p>Fetching user details...</p>
                </div>
                
                <!-- Dynamic Form (populated by JS) -->
                <div class="authme-user-v-form-wrapper" style="display:none;" id="authme-user-v-form-container">
                    
                    <!-- Profile Photo Section -->
                    <div class="authme-user-v-avatar-section">
                        <div class="authme-user-v-avatar-wrapper">
                            <img src="" alt="Profile Photo" id="authme-user-v-avatar-img">
                            <button type="button" class="authme-user-v-avatar-edit" id="authme-user-v-avatar-edit-btn" title="Change Profile Photo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </button>
                            <input type="hidden" id="authme-user-v-avatar-id">
                        </div>
                    </div>

                    <div class="authme-user-v-section">
                        <h3 class="authme-user-v-section-title">Personal Information</h3>
                        <div class="authme-user-v-grid">
                            <div class="authme-user-v-field">
                                <label for="authme-user-v-user-id">User ID</label>
                                <input type="text" id="authme-user-v-user-id" disabled readonly class="authme-user-v-disabled-input">
                                <small class="authme-user-v-hint">Actual system ID (cannot be changed)</small>
                            </div>
                            <div class="authme-user-v-field">
                                <label for="authme-user-v-fullname">Full Name</label>
                                <input type="text" id="authme-user-v-fullname" placeholder="Enter full name">
                            </div>
                            <div class="authme-user-v-field">
                                <label for="authme-user-v-username">Username</label>
                                <div class="authme-user-v-input-with-status">
                                    <input type="text" id="authme-user-v-username" placeholder="Enter username">
                                    <div class="authme-user-v-status-icon" id="authme-user-v-username-status"></div>
                                </div>
                                <small class="authme-user-v-hint" id="authme-user-v-username-msg">Enter a unique username</small>
                            </div>
                            <div class="authme-user-v-field">
                                <label for="authme-user-v-email">Email Address</label>
                                <input type="email" id="authme-user-v-email" placeholder="Enter email">
                            </div>
                            <div class="authme-user-v-field">
                                <label for="authme-user-v-mobile">Mobile Number</label>
                                <input type="text" id="authme-user-v-mobile" placeholder="Enter mobile number">
                            </div>
                        </div>
                    </div>

                    <div class="authme-user-v-section">
                        <h3 class="authme-user-v-section-title">Security Settings</h3>
                        <div class="authme-user-v-grid">
                            <div class="authme-user-v-field authme-user-v-full-width">
                                <label for="authme-user-v-password">Change Password</label>
                                <div class="authme-user-v-pass-wrap">
                                    <input type="password" id="authme-user-v-password" placeholder="Enter new password to change">
                                    <button type="button" class="authme-user-v-toggle-pass" id="authme-user-v-toggle-pass">Show</button>
                                </div>
                                <small class="authme-user-v-hint">Leave blank if you don't want to change the password.</small>
                            </div>
                        </div>
                    </div>

                    <div class="authme-user-v-footer">
                        <button type="button" class="authme-user-v-save-btn" id="authme-user-v-save-btn">
                            <span class="authme-user-v-btn-text">Update Profile</span>
                            <div class="authme-user-v-btn-spinner" style="display:none;"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
