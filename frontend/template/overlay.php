<?php
/**
 * AuthMe Overlay Container
 *
 * Main overlay wrapper that houses the login, register,
 * and OTP screens. Rendered hidden and opened via JS.
 * Uses position:fixed + z-index:1000 for modal popup.
 *
 * @package AuthMe
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="authme-global-plugin-wrapper">
    <!-- AuthMe Overlay Backdrop + Container -->
    <div id="authme-overlay-backdrop" class="authme-overlay-backdrop" style="display:none;">

        <div id="authme-overlay-container" class="authme-overlay-container">

            <!-- Close Button -->
            <button type="button" id="authme-overlay-close" class="authme-overlay-close" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Login Screen -->
            <?php 
            $tpl = AuthMe_Assets_Loader::dir('tpl_login');
            if ($tpl && file_exists($tpl)) include $tpl; 
            ?>

            <!-- Register Screen -->
            <?php 
            $tpl = AuthMe_Assets_Loader::dir('tpl_register');
            if ($tpl && file_exists($tpl)) include $tpl; 
            ?>

            <!-- OTP Verification Screen (shared for login, registration, password reset) -->
            <?php 
            $tpl = AuthMe_Assets_Loader::dir('tpl_otp');
            if ($tpl && file_exists($tpl)) include $tpl; 
            ?>

            <!-- Forgot Password Screen -->
            <?php 
            $tpl = AuthMe_Assets_Loader::dir('tpl_forgot_password');
            if ($tpl && file_exists($tpl)) include $tpl; 
            ?>

            <!-- New Password Screen (shown after OTP verified on reset flow) -->
            <?php 
            $tpl = AuthMe_Assets_Loader::dir('tpl_new_password');
            if ($tpl && file_exists($tpl)) include $tpl; 
            ?>

        </div>

    </div>
</div>

