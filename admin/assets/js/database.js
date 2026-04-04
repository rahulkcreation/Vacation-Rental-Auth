/**
 * AuthMe Admin — Database Management JavaScript
 *
 * Handles DB status checking, table creation via AJAX,
 * and renders the status table UI.
 * Uses AuthMe_DB_Schema on the backend for all DB operations.
 *
 * @package AuthMe
 */
(function () {
    'use strict';

    /* ── Toast Helper ────────────────────── */

    /**
     * Show a toast notification.
     *
     * @param {string} message - Toast text.
     * @param {string} type    - 'success' or 'error'.
     */
    function showToast(message, type) {
        var existing = document.querySelector('.am-db-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'am-db-toast am-db-toast-' + type;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function () {
            if (toast && toast.parentNode) toast.remove();
        }, 4000);
    }

    /* ── Status Table Renderer ───────────── */

    /**
     * Render the database status data into an HTML table.
     *
     * @param {object} data - Status data from AJAX response.
     */
    function renderStatusTable(data) {
        var container = document.getElementById('am-db-status');
        if (!container) return;

        var html = '';

        html += '<table class="am-db-table">';
        html += '<thead><tr><th>Item</th><th>Status</th></tr></thead>';
        html += '<tbody>';

        /* ── OTP Table ─────────────── */
        var otpExists = data.otp_table && data.otp_table.exists;
        html += '<tr>';
        html += '<td class="am-db-table-row">' + (data.otp_table ? data.otp_table.name : 'wp_authme_otp_storage') + '</td>';
        html += '<td class="' + (otpExists ? 'am-db-status-ok' : 'am-db-status-missing') + '">';
        html += otpExists ? '✅ Exists' : '❌ Missing';
        html += '</td></tr>';

        // OTP columns
        if (otpExists && data.otp_table.columns) {
            var cols = data.otp_table.columns;
            for (var col in cols) {
                if (cols.hasOwnProperty(col)) {
                    html += '<tr>';
                    html += '<td class="am-db-col-name"><span class="am-db-col-arrow">↳</span>' + col + '</td>';
                    html += '<td class="' + (cols[col] ? 'am-db-status-ok' : 'am-db-status-missing') + '">';
                    html += cols[col] ? '✅ OK' : '❌ Missing';
                    html += '</td></tr>';
                }
            }
        }

        /* ── Host Request Table ─────── */
        var hostExists = data.host_table && data.host_table.exists;
        html += '<tr>';
        html += '<td class="am-db-table-row">' + (data.host_table ? data.host_table.name : 'wp_host_request') + '</td>';
        html += '<td class="' + (hostExists ? 'am-db-status-ok' : 'am-db-status-missing') + '">';
        html += hostExists ? '✅ Exists' : '❌ Missing';
        html += '</td></tr>';

        // Host columns
        if (hostExists && data.host_table.columns) {
            var hCols = data.host_table.columns;
            for (var hCol in hCols) {
                if (hCols.hasOwnProperty(hCol)) {
                    html += '<tr>';
                    html += '<td class="am-db-col-name"><span class="am-db-col-arrow">↳</span>' + hCol + '</td>';
                    html += '<td class="' + (hCols[hCol] ? 'am-db-status-ok' : 'am-db-status-missing') + '">';
                    html += hCols[hCol] ? '✅ OK' : '❌ Missing';
                    html += '</td></tr>';
                }
            }
        }

        html += '</tbody></table>';

        // All-good or warning banner
        if (data.all_good) {
            html += '<div class="am-db-banner-ok">✅ All tables and columns are correctly set up.</div>';
        } else {
            html += '<div class="am-db-banner-warn">⚠️ Some tables or columns are missing. Click "Create / Update Tables" to fix.</div>';
        }

        container.innerHTML = html;
    }

    /* ── AJAX: Fetch DB Status ───────────── */

    function fetchDbStatus() {
        var container = document.getElementById('am-db-status');
        if (!container) return;

        container.innerHTML = '<p class="am-db-loading">Checking database status…</p>';

        var formData = new FormData();
        formData.append('action', 'authme_admin_check_db');
        formData.append('nonce', authme_admin.nonce);

        fetch(authme_admin.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            if (result.success) {
                renderStatusTable(result.data);
            } else {
                container.innerHTML = '<div class="am-db-banner-warn">Failed to check database status.</div>';
            }
        })
        .catch(function () {
            container.innerHTML = '<div class="am-db-banner-warn">Network error. Please try again.</div>';
        });
    }

    /* ── AJAX: Create / Update Tables ────── */

    function createTables() {
        var btn = document.getElementById('am-db-btn-create');
        if (!btn) return;

        btn.disabled = true;
        btn.textContent = 'Creating…';

        var formData = new FormData();
        formData.append('action', 'authme_admin_create_tables');
        formData.append('nonce', authme_admin.nonce);

        fetch(authme_admin.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            btn.disabled = false;
            btn.textContent = 'Create / Update Tables';

            if (result.success) {
                showToast(result.data.message, 'success');
                fetchDbStatus(); // Refresh status
            } else {
                showToast(result.data.message || 'An error occurred.', 'error');
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.textContent = 'Create / Update Tables';
            showToast('Network error. Please try again.', 'error');
        });
    }

    /* ── Init on DOM Ready ──────────────── */

    document.addEventListener('DOMContentLoaded', function () {

        // Only run on the database page
        if (!document.getElementById('am-database')) return;

        // Auto-check status on load
        fetchDbStatus();

        // Create / Update Tables button
        var createBtn = document.getElementById('am-db-btn-create');
        if (createBtn) {
            createBtn.addEventListener('click', createTables);
        }

        // Refresh Status button
        var refreshBtn = document.getElementById('am-db-btn-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', fetchDbStatus);
        }
    });

})();
