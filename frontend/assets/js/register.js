/**
 * AuthMe — Registration Form JavaScript
 *
 * Handles the registration flow:
 *   1. Real-time username availability check (debounced AJAX)
 *   2. Real-time email availability check (debounced AJAX)
 *   3. Country code dropdown population & mobile validation
 *   4. Password strength indicator
 *   5. Confirm password match
 *   6. "Send OTP" → stores user data, sends OTP, switches to OTP screen
 *
 * Flow: Register Form → Send OTP → OTP Screen → Verify OTP → Auto-create User
 *
 * @package AuthMe
 */

/* global authmeAjax, authmeIsValidEmail, authmeSetFieldState, authmeValidatePassword, authmePasswordStrength, authmeToast, authmeShowScreen */

(function () {
    'use strict';

    /* ── Local State ────────────────────────── */
    var regState = {
        usernameValid: false,
        emailValid: false,
        mobileValid: false,
        passwordValid: false,
        confirmValid: false,
    };

    /* ── Debounce Timers ─────────────────── */
    var usernameDebounce = null;
    var emailDebounce    = null;
    var mobileDebounce   = null;

    /**
     * Reset the internal registration validation state.
     * Called by clearAllForms() in overlay.js when the overlay closes
     * or when the user switches away from the register screen.
     */
    function resetRegState() {
        regState.usernameValid = false;
        regState.emailValid    = false;
        regState.mobileValid   = false;
        regState.passwordValid = false;
        regState.confirmValid  = false;

        // Also reset the country select back to India (+91) default
        var countrySelect = document.getElementById('authme-reg-country-code');
        var mobileInput   = document.getElementById('authme-reg-mobile');
        if (countrySelect) {
            var defaultCode = '+91';
            for (var i = 0; i < countrySelect.options.length; i++) {
                if (countrySelect.options[i].value === defaultCode) {
                    countrySelect.selectedIndex = i;
                    break;
                }
            }
            // Update the placeholder to the default country's example
            if (mobileInput) {
                var selectedOpt = countrySelect.options[countrySelect.selectedIndex];
                if (selectedOpt && selectedOpt.dataset.example) {
                    mobileInput.placeholder = 'e.g. ' + selectedOpt.dataset.example;
                }
            }
        }

        // Reset the password strength bar
        var strengthEl = document.getElementById('authme-reg-password-strength');
        if (strengthEl) {
            strengthEl.innerHTML = '';
        }

        // Disable submit button
        var submitBtn = document.getElementById('authme-reg-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Send OTP';
        }
    }

    // Expose globally so overlay.js clearAllForms() can call it
    window.authmeResetRegState = resetRegState;

    /* ── DOM Ready ───────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {

        var usernameInput  = document.getElementById('authme-reg-username');
        var emailInput     = document.getElementById('authme-reg-email');
        var mobileInput    = document.getElementById('authme-reg-mobile');
        var countrySelect  = document.getElementById('authme-reg-country-code');
        var passwordInput  = document.getElementById('authme-reg-password');
        var confirmInput   = document.getElementById('authme-reg-confirm-password');
        var submitBtn      = document.getElementById('authme-reg-submit-btn');
        var form           = document.getElementById('authme-register-form');

        if (!usernameInput || !emailInput || !mobileInput || !countrySelect || !passwordInput || !confirmInput || !submitBtn || !form) return;

        var usernameMsg  = document.getElementById('authme-reg-username-msg');
        var emailMsg     = document.getElementById('authme-reg-email-msg');
        var mobileMsg    = document.getElementById('authme-reg-mobile-msg');
        var passwordMsg  = document.getElementById('authme-reg-password-msg');
        var confirmMsg   = document.getElementById('authme-reg-confirm-password-msg');
        var strengthEl   = document.getElementById('authme-reg-password-strength');

        /* ── Username Validation ─────────────── */
        usernameInput.addEventListener('input', function () {
            var value = this.value.trim();
            regState.usernameValid = false;
            updateSubmitButton();

            if (!value) {
                authmeSetFieldState(usernameInput, usernameMsg, '', '');
                return;
            }

            // Client-side checks first
            if (!/^[a-zA-Z]/.test(value)) {
                authmeSetFieldState(usernameInput, usernameMsg, 'error', 'Username must start with an alphabet character.');
                return;
            }
            if (!/^[a-zA-Z][a-zA-Z0-9]{3,13}$/.test(value)) {
                authmeSetFieldState(usernameInput, usernameMsg, 'error', 'Username must be 4–14 alphanumeric characters.');
                return;
            }

            // Debounced AJAX uniqueness check
            clearTimeout(usernameDebounce);
            usernameDebounce = setTimeout(function () {
                authmeAjax('authme_check_username', { username: value },
                    function (data) {
                        regState.usernameValid = true;
                        authmeSetFieldState(usernameInput, usernameMsg, 'success', data.message);
                        updateSubmitButton();
                    },
                    function (data) {
                        regState.usernameValid = false;
                        authmeSetFieldState(usernameInput, usernameMsg, 'error', data.message);
                        updateSubmitButton();
                    }
                );
            }, 500);
        });

        /* ── Email Validation ────────────────── */
        emailInput.addEventListener('input', function () {
            var value = this.value.trim();
            regState.emailValid = false;
            updateSubmitButton();

            if (!value) {
                authmeSetFieldState(emailInput, emailMsg, '', '');
                return;
            }

            // Client-side format check first
            if (!authmeIsValidEmail(value)) {
                authmeSetFieldState(emailInput, emailMsg, 'error', 'Please enter a valid email address.');
                return;
            }

            // Debounced AJAX uniqueness check
            clearTimeout(emailDebounce);
            emailDebounce = setTimeout(function () {
                authmeAjax('authme_check_email', { email: value },
                    function (data) {
                        regState.emailValid = true;
                        authmeSetFieldState(emailInput, emailMsg, 'success', data.message);
                        updateSubmitButton();
                    },
                    function (data) {
                        regState.emailValid = false;
                        authmeSetFieldState(emailInput, emailMsg, 'error', data.message);
                        updateSubmitButton();
                    }
                );
            }, 500);
        });

        /* ── Country Code Dropdown Population ── */
        populateCountryDropdown();

        /**
         * Populate the country code <select> with options from
         * the authmeCountryPhoneData global array.
         *
         * Each option displays: Flag + Country Name (Code)
         * e.g. "🇮🇳 India (+91)"
         */
        function populateCountryDropdown() {
            if (!window.authmeCountryPhoneData || !countrySelect) return;

            // Clear all existing dynamic options (keep the placeholder at index 0)
            while (countrySelect.options.length > 1) {
                countrySelect.remove(1);
            }

            // Add country options
            window.authmeCountryPhoneData.forEach(function (country) {
                var option = document.createElement('option');
                option.value = country.code;
                // Display format: "🇮🇳 +91" — compact and clean
                option.textContent = country.flag + ' ' + country.code;
                option.dataset.example = country.example;
                option.dataset.region = country.region;
                countrySelect.appendChild(option);
            });

            // Auto-select India (+91) as default
            var defaultCode = '+91';
            for (var i = 0; i < countrySelect.options.length; i++) {
                if (countrySelect.options[i].value === defaultCode) {
                    countrySelect.selectedIndex = i;
                    break;
                }
            }

            // Update mobile input placeholder based on selected country
            var selectedOption = countrySelect.options[countrySelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.example) {
                mobileInput.placeholder = 'e.g. ' + selectedOption.dataset.example;
            }
        }

        // Country select change handler
        countrySelect.addEventListener('change', function () {
            var changedOption = countrySelect.options[countrySelect.selectedIndex];
            if (changedOption && changedOption.dataset.example) {
                mobileInput.placeholder = 'e.g. ' + changedOption.dataset.example;
            }
            // Re-validate mobile if it already has a value
            if (mobileInput.value) {
                validateMobileNumber();
            }
        });

        /* ── Mobile Number Validation ─────────── */
        function validateMobileNumber() {
            var countryCode    = countrySelect.value;
            var mobileValue    = mobileInput.value.trim();
            var selectedCountry = null;

            // Check if a country code is actually selected (not the placeholder)
            if (!countryCode) {
                regState.mobileValid = false;
                if (mobileValue) {
                    authmeSetFieldState(mobileInput, mobileMsg, 'error', 'Please select a country code first.');
                } else {
                    authmeSetFieldState(mobileInput, mobileMsg, '', '');
                }
                updateSubmitButton();
                return;
            }

            // Find the selected country's data
            if (window.authmeCountryPhoneData) {
                window.authmeCountryPhoneData.forEach(function (country) {
                    if (country.code === countryCode) {
                        selectedCountry = country;
                    }
                });
            }

            if (!mobileValue) {
                regState.mobileValid = false;
                authmeSetFieldState(mobileInput, mobileMsg, '', '');
                updateSubmitButton();
                return;
            }

            if (!selectedCountry) {
                regState.mobileValid = false;
                authmeSetFieldState(mobileInput, mobileMsg, 'error', 'Please select a valid country code.');
                updateSubmitButton();
                return;
            }

            // Validate the mobile number against the country's regex pattern
            if (selectedCountry.regex.test(mobileValue)) {
                // Formatting is good, now check uniqueness via AJAX
                clearTimeout(mobileDebounce);
                mobileDebounce = setTimeout(function () {
                    authmeAjax('authme_check_mobile', { mobile: countryCode + mobileValue },
                        function (data) {
                            regState.mobileValid = true;
                            authmeSetFieldState(mobileInput, mobileMsg, 'success', data.message);
                            updateSubmitButton();
                        },
                        function (data) {
                            regState.mobileValid = false;
                            authmeSetFieldState(mobileInput, mobileMsg, 'error', data.message);
                            updateSubmitButton();
                        }
                    );
                }, 500);
            } else {
                regState.mobileValid = false;
                authmeSetFieldState(mobileInput, mobileMsg, 'error', 'Invalid mobile number format for ' + selectedCountry.country + '.');
                updateSubmitButton();
            }
        }

        mobileInput.addEventListener('input', function () {
            validateMobileNumber();
        });

        /* ── Password Validation + Strength ──── */
        passwordInput.addEventListener('input', function () {
            var value = this.value;
            regState.passwordValid = false;

            // Update strength indicator
            updateStrengthBar(value);

            if (!value) {
                authmeSetFieldState(passwordInput, passwordMsg, '', '');
                updateSubmitButton();
                return;
            }

            var result = authmeValidatePassword(value);
            if (result.valid) {
                regState.passwordValid = true;
                authmeSetFieldState(passwordInput, passwordMsg, 'success', result.message);
            } else {
                authmeSetFieldState(passwordInput, passwordMsg, 'error', result.message);
            }

            // Re-validate confirm password if it has a value
            if (confirmInput.value) {
                validateConfirmPassword();
            }

            updateSubmitButton();
        });

        /* ── Confirm Password Validation ─────── */
        confirmInput.addEventListener('input', function () {
            validateConfirmPassword();
            updateSubmitButton();
        });

        function validateConfirmPassword() {
            var value = confirmInput.value;
            regState.confirmValid = false;

            if (!value) {
                authmeSetFieldState(confirmInput, confirmMsg, '', '');
                return;
            }

            if (value !== passwordInput.value) {
                authmeSetFieldState(confirmInput, confirmMsg, 'error', 'Passwords do not match.');
            } else {
                regState.confirmValid = true;
                authmeSetFieldState(confirmInput, confirmMsg, 'success', 'Passwords match.');
            }
        }

        /* ── Strength Bar ────────────────────── */
        function updateStrengthBar(password) {
            if (!strengthEl) return;

            if (!password) {
                strengthEl.innerHTML = '';
                return;
            }

            var level = authmePasswordStrength(password);

            // Create the bar element if not already present
            var bar = strengthEl.querySelector('.authme-strength-bar');
            if (!bar) {
                bar = document.createElement('div');
                bar.className = 'authme-strength-bar';
                strengthEl.innerHTML = '';
                strengthEl.appendChild(bar);
            }

            // Reset all level classes
            bar.className = 'authme-strength-bar authme-strength-' + level;
        }

        /* ── Submit Button State ─────────────── */
        function updateSubmitButton() {
            submitBtn.disabled = !(
                regState.usernameValid &&
                regState.emailValid &&
                regState.mobileValid &&
                regState.passwordValid &&
                regState.confirmValid
            );
        }

        /* ── Form Submission (Send OTP) ──────── */
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (submitBtn.disabled) return;

            var username = usernameInput.value.trim();
            var email    = emailInput.value.trim();
            var countryCode = countrySelect.value;
            var mobileNumber = mobileInput.value.trim();
            var password = passwordInput.value;

            // Build full international mobile number (e.g., +919876543210)
            var fullMobile = countryCode + mobileNumber;

            // Get region code from the selected option's data attribute
            var selectedOpt = countrySelect.options[countrySelect.selectedIndex];
            var mobileRegion = (selectedOpt && selectedOpt.dataset.region) ? selectedOpt.dataset.region : '';

            // Build user_data JSON
            var userData = JSON.stringify({
                username: username,
                email:    email,
                mobile_number: fullMobile,
                mobile_region: mobileRegion,
                password: password,
            });

            // Disable button & show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending OTP…';

            // Send OTP for registration
            authmeAjax('authme_send_otp', {
                email:     email,
                purpose:   'registration',
                user_data: userData,
            },
            function (data) {
                authmeToast('success', data.message);

                // Store context for OTP screen
                document.getElementById('authme-otp-email').value     = email;
                document.getElementById('authme-otp-purpose').value   = 'registration';
                document.getElementById('authme-otp-user-data').value = userData;
                document.getElementById('authme-otp-user-id').value   = '';
                document.getElementById('authme-otp-remember').value  = document.getElementById('authme-reg-remember').checked ? 'true' : 'false';

                // Switch to OTP screen
                authmeShowScreen('authme-otp-screen');

                // Start the OTP timer
                if (typeof window.authmeStartOtpTimer === 'function') {
                    window.authmeStartOtpTimer();
                }

                // Focus the first OTP box
                var firstBox = document.querySelector('#authme-otp-screen .authme-otp-box');
                if (firstBox) firstBox.focus();

                // Reset button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send OTP';
            },
            function (data) {
                authmeToast('error', data.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send OTP';
            });
        });

    });

})();
