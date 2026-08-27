(function () {
    'use strict';

    var modalElement = document.getElementById('pilgrim-delete-modal');

    if (!modalElement) {
        return;
    }

    var loadingEl = document.getElementById('pilgrim-delete-loading');
    var errorEl = document.getElementById('pilgrim-delete-error');
    var contentEl = document.getElementById('pilgrim-delete-content');
    var confirmButton = document.getElementById('pilgrim-delete-confirm');
    var modalInstance = null;
    var activeForm = null;

    function getBootstrapModal() {
        var bootstrapLib = window.bootstrap;

        if (!bootstrapLib || !bootstrapLib.Modal) {
            return null;
        }

        if (modalInstance) {
            return modalInstance;
        }

        if (typeof bootstrapLib.Modal.getOrCreateInstance === 'function') {
            modalInstance = bootstrapLib.Modal.getOrCreateInstance(modalElement);
        } else {
            modalInstance = new bootstrapLib.Modal(modalElement);
        }

        return modalInstance;
    }

    function setVisible(element, visible) {
        if (!element) {
            return;
        }

        element.classList.toggle('d-none', !visible);
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderDetails(pilgrim) {
        var detailsEl = document.getElementById('pilgrim-delete-details');
        var rows = [
            ['Full Name', pilgrim.full_name],
            ['Passport', pilgrim.passport_no],
            ['Family Code', pilgrim.family_code],
            ['Hajj Year', pilgrim.hajj_year],
            ['Company', pilgrim.company],
            ['Package', pilgrim.package],
            ['POD', pilgrim.pod_city],
            ['Gender', pilgrim.gender],
        ];

        detailsEl.innerHTML = rows.map(function (row) {
            var label = row[0];
            var value = row[1];

            return '<div class="pilgrim-delete-details__row">'
                + '<dt>' + escapeHtml(label) + '</dt>'
                + '<dd>' + escapeHtml(value && String(value).trim() !== '' ? value : '—') + '</dd>'
                + '</div>';
        }).join('');
    }

    function renderFamily(family) {
        var summaryEl = document.getElementById('pilgrim-delete-family-summary');
        var membersEl = document.getElementById('pilgrim-delete-family-members');
        var rebalanceSection = document.getElementById('pilgrim-delete-rebalance-section');
        var rebalanceRows = document.getElementById('pilgrim-delete-rebalance-rows');

        summaryEl.textContent = family.summary || '';

        if (!family.other_members || family.other_members.length === 0) {
            membersEl.innerHTML = '';
            setVisible(membersEl, false);
        } else {
            membersEl.innerHTML = family.other_members.map(function (member) {
                return '<li><strong>' + escapeHtml(member.full_name || 'Unnamed') + '</strong> — '
                    + escapeHtml(member.family_code || '—') + '</li>';
            }).join('');
            setVisible(membersEl, true);
        }

        if (family.changes && family.changes.length > 0) {
            rebalanceRows.innerHTML = family.changes.map(function (change) {
                var afterClass = change.will_change ? 'pilgrim-delete-code--changed' : '';

                return '<tr>'
                    + '<td>' + escapeHtml(change.full_name || 'Unnamed') + '</td>'
                    + '<td>' + escapeHtml(change.current_family_code || '—') + '</td>'
                    + '<td class="' + afterClass + '">' + escapeHtml(change.new_family_code || '—') + '</td>'
                    + '</tr>';
            }).join('');
            setVisible(rebalanceSection, true);
        } else {
            rebalanceRows.innerHTML = '';
            setVisible(rebalanceSection, false);
        }
    }

    function renderFlights(flights) {
        var summaryEl = document.getElementById('pilgrim-delete-flights-summary');
        var listEl = document.getElementById('pilgrim-delete-flights-list');

        if (!flights || flights.length === 0) {
            summaryEl.textContent = 'No flight assignments will be affected.';
            listEl.innerHTML = '';
            setVisible(listEl, false);

            return;
        }

        summaryEl.textContent = flights.length === 1
            ? 'This registration will be removed from the following flight:'
            : 'This registration will be removed from the following flights:';

        listEl.innerHTML = flights.map(function (flight) {
            return '<li><strong>' + escapeHtml(flight.label) + '</strong></li>';
        }).join('');
        setVisible(listEl, true);
    }

    function resetModalUi() {
        setVisible(loadingEl, true);
        setVisible(errorEl, false);
        setVisible(contentEl, false);
        setVisible(confirmButton, false);
        errorEl.textContent = '';
    }

    function clearActiveForm() {
        activeForm = null;
    }

    function showError(message) {
        setVisible(loadingEl, false);
        setVisible(contentEl, false);
        setVisible(confirmButton, false);
        errorEl.textContent = message;
        setVisible(errorEl, true);
    }

    function showPreview(data) {
        renderDetails(data.pilgrim);
        renderFamily(data.family);
        renderFlights(data.flights);

        setVisible(loadingEl, false);
        setVisible(errorEl, false);
        setVisible(contentEl, true);
        setVisible(confirmButton, true);
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-pilgrim-delete-trigger]');

        if (!trigger) {
            return;
        }

        event.preventDefault();

        var modal = getBootstrapModal();

        if (!modal) {
            if (window.confirm('Delete this registration?')) {
                var fallbackForm = trigger.closest('[data-pilgrim-delete-form]');

                if (fallbackForm) {
                    fallbackForm.submit();
                }
            }

            return;
        }

        var previewUrl = trigger.getAttribute('data-preview-url');
        activeForm = trigger.closest('[data-pilgrim-delete-form]');

        if (!previewUrl || !activeForm) {
            return;
        }

        resetModalUi();
        modal.show();

        fetch(previewUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load delete preview.');
                }

                return response.json();
            })
            .then(showPreview)
            .catch(function () {
                showError('Unable to load delete preview. Please try again.');
            });
    });

    confirmButton.addEventListener('click', function () {
        if (!activeForm) {
            return;
        }

        if (typeof activeForm.requestSubmit === 'function') {
            activeForm.requestSubmit();
        } else {
            activeForm.submit();
        }
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        clearActiveForm();
        resetModalUi();
    });
})();
