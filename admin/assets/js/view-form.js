/**
 * AuthMe Admin — View Form JavaScript
 *
 * Handles fetching, populating, and updating status
 * for the decoupled single host request View Form.
 * Matches `view-form.html` design using `.amv-` classes.
 *
 * @package AuthMe
 */

(function () {
    'use strict';

    /* ── SVG Icons ───────────────────────── */
    var iconDoc = '<svg class="amv-icon-doc" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
    var iconExtLink = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';

    var currentHostId = null;

    /* ── DOM Init ────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('amv-view-container');
        if (!container) return;

        currentHostId = parseInt(container.getAttribute('data-host-id'), 10);
        
        if (currentHostId) {
            loadApplicationData(currentHostId);
        } else {
            showError("Invalid Application ID. Please go back and try again.");
        }
    });

    /* ── Navigation ──────────────────────── */
    window.amvGoBack = function() {
        // Find if we have a referrer inside the plugin, or just navigate to host requests home
        if (document.referrer && document.referrer.includes('page=authme-host-requests')) {
            window.history.back();
        } else {
            window.location.href = '?page=authme-host-requests';
        }
    };

    /* ── Fetch Data ──────────────────────── */
    function loadApplicationData(id) {
        var formData = new FormData();
        formData.append('action', 'authme_admin_get_single_host');
        formData.append('nonce', authme_admin.nonce);
        formData.append('id', id);

        fetch(authme_admin.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            document.getElementById('amv-loading-state').style.display = 'none';

            if (result.success) {
                populateViewForm(result.data);
            } else {
                showError(result.data.message || 'Failed to load data.');
            }
        })
        .catch(function () {
            document.getElementById('amv-loading-state').style.display = 'none';
            showError('Network error. Please try again.');
        });
    }

    function showError(msg) {
        var errEl = document.getElementById('amv-error-state');
        errEl.innerText = msg;
        errEl.style.display = 'block';
    }

    /* ── Populate UI ─────────────────────── */
    function populateViewForm(data) {
        var basicData = data.userData || {};
        basicData.status = data.status; // Merge status from root object
        var dbId = data.raw_id;

        // Names & Emails
        var name = basicData.fullname || basicData.username || 'N/A';
        document.getElementById('amv-view-name').textContent = name;
        document.getElementById('amv-view-email').textContent = basicData.email || 'N/A';
        
        // Initials Avatar
        var initial = name.charAt(0).toUpperCase();
        document.getElementById('amv-view-initials').textContent = initial || '--';

        // Details
        document.getElementById('amv-view-id').textContent = dbId;
        document.getElementById('amv-view-phone').textContent = basicData.mobile || basicData.phone || 'N/A';

        // Documents
        var docContainer = document.getElementById('amv-view-docs-list');
        docContainer.innerHTML = '';
        var hasDocs = false;
        
        // Match specific json keys submitted by frontend JS
        var docsObj = basicData.documents || {};
        var docMap = {
            'aadharf': 'Aadhar Card (Front)',
            'aadharb': 'Aadhar Card (Back)',
            'pan': 'PAN Card'
        };

        for (var key in docMap) {
            if (docMap.hasOwnProperty(key) && docsObj[key]) {
                hasDocs = true;
                var docUrl = docsObj[key]; // Usually base64 encoded image

                var docHtml = 
                    '<div class="amv-doc-item">' +
                        '<div class="amv-doc-name">' + iconDoc + docMap[key] + '</div>' +
                        '<a href="' + docUrl + '" target="_blank" class="amv-btn-view">View ' + iconExtLink + '</a>' +
                    '</div>';
                
                docContainer.insertAdjacentHTML('beforeend', docHtml);
            }
        }

        if (!hasDocs) {
            docContainer.innerHTML = '<p style="font-size: 0.875rem; color: var(--authme-grey-light-text); margin:0;">No documents uploaded.</p>';
        }

        // Status Badge styling
        var badge = document.getElementById('amv-view-status');
        badge.className = 'amv-status-badge'; // Reset entirely
        
        if (basicData.status === 'pending') badge.classList.add('amv-status-badge-pending');
        if (basicData.status === 'approved') badge.classList.add('amv-status-badge-approved');
        if (basicData.status === 'rejected') badge.classList.add('amv-status-badge-rejected');
        
        badge.textContent = basicData.status;

        // Action Buttons
        var actionWrap = document.getElementById('amv-view-actions');
        actionWrap.innerHTML = ''; // reset

        if (basicData.status === 'pending') {
            actionWrap.innerHTML = 
                '<button class="amv-btn-update amv-btn-reject" onclick="amvUpdateHostStatus(' + dbId + ', \'rejected\')">' +
                    '<svg class="amv-btn-svg" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
                    'Reject' +
                '</button>' +
                '<button class="amv-btn-update amv-btn-approve" onclick="amvUpdateHostStatus(' + dbId + ', \'approved\')">' +
                    '<svg class="amv-btn-svg" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                    'Approve' +
                '</button>';
        } else {
            var actionText = (basicData.status === 'approved') ? 'Application Approved' : 'Application Rejected';
            actionWrap.innerHTML = '<div class="amv-action-completed">' + actionText + '</div>';
        }

        // Show all wraps
        document.getElementById('amv-profile-wrap').style.display = 'flex';
        document.getElementById('amv-details-wrap').style.display = 'block';
        document.getElementById('amv-docs-wrap').style.display = 'block';
        document.getElementById('amv-status-wrap').style.display = 'block';
        actionWrap.style.display = 'flex';
    }

    /* ── Action Handler ──────────────────── */
    window.amvUpdateHostStatus = function(id, newStatus) {
        var actionLabel = newStatus === 'approved' ? 'Approve' : 'Reject';
        
        if (window.authmeShowAdminConfirm) {
            window.authmeShowAdminConfirm(
                actionLabel + ' Application',
                'Are you sure you want to ' + actionLabel.toLowerCase() + ' this application?',
                function() {
                    executeStatusUpdate(id, newStatus);
                }
            );
        } else {
            if (confirm('Are you sure you want to ' + actionLabel.toLowerCase() + ' this application?')) {
                executeStatusUpdate(id, newStatus);
            }
        }
    };

    function executeStatusUpdate(id, newStatus) {
        // Disable buttons temporarily
        var actionsWrap = document.getElementById('amv-view-actions');
        var originalHtml = actionsWrap.innerHTML;
        actionsWrap.innerHTML = '<div class="amv-action-completed">Processing...</div>';

        var formData = new FormData();
        formData.append('action', 'authme_admin_process_host');
        formData.append('nonce', authme_admin.nonce);
        formData.append('id', id);
        formData.append('status', newStatus);

        fetch(authme_admin.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            if (result.success) {
                if (window.authmeShowToaster) {
                    window.authmeShowToaster('Success', 'Application ' + newStatus + ' successfully.');
                }
                // Reload the same ID to fetch updated data nicely
                document.getElementById('amv-profile-wrap').style.display = 'none';
                document.getElementById('amv-details-wrap').style.display = 'none';
                document.getElementById('amv-docs-wrap').style.display = 'none';
                document.getElementById('amv-status-wrap').style.display = 'none';
                actionsWrap.style.display = 'none';
                document.getElementById('amv-loading-state').style.display = 'block';
                loadApplicationData(id);
            } else {
                actionsWrap.innerHTML = originalHtml; // Revert
                if (window.authmeShowToaster) {
                    window.authmeShowToaster('Error', result.data.message || 'Operation failed.');
                }
            }
        })
        .catch(function () {
            actionsWrap.innerHTML = originalHtml; // Revert
            if (window.authmeShowToaster) {
                window.authmeShowToaster('Network Error', 'Please try again.');
            }
        });
    }

})();
