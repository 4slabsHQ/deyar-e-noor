(function ($) {
    'use strict';

    var page = document.getElementById('flight-assignment-page');
    if (!page) {
        return;
    }

    var workspaceContainer = document.getElementById('flight-assignment-workspace');

    function initDataTable($table) {
        if (window.deyarInitDataTable) {
            window.deyarInitDataTable($table);

            return;
        }

        if (!$table.length || $.fn.DataTable.isDataTable($table)) {
            return;
        }

        $table.addClass('admin-index-table');

        $table.DataTable({
            autoWidth: false,
            scrollX: $table.data('scrollX') || $table.data('scroll-x') || false,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
                },
                emptyTable: $table.data('empty-message') || 'No records found.',
            },
            columnDefs: [
                { orderable: false, targets: 'no-sort' },
            ],
        });
    }

    function destroyResultsTable() {
        var resultsContainer = getResultsContainer();

        if (!resultsContainer || !$) {
            return;
        }

        $(resultsContainer).find('table[data-datatable]').each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
    }

    function initResultsTable() {
        var resultsContainer = getResultsContainer();

        if (!resultsContainer) {
            return;
        }

        $(resultsContainer).find('table[data-datatable]').each(function () {
            initDataTable($(this));
        });
    }

    function getResultsContainer() {
        if (!workspaceContainer) {
            return null;
        }

        return workspaceContainer.querySelector('#flight-assignment-results');
    }

    function getActiveWorkspace() {
        if (!workspaceContainer) {
            return null;
        }

        return workspaceContainer.querySelector('.flight-assignment-workspace');
    }

    function updateSelectedFlightRow(flightId) {
        page.querySelectorAll('.flight-assignment-row').forEach(function (row) {
            row.classList.toggle('is-selected', row.getAttribute('data-flight-id') === String(flightId));
        });
    }

    function updateBrowserUrl(flightId, filterParams) {
        var url = new URL(window.location.href);

        url.searchParams.set('flight', flightId);

        [
            'company_id',
            'pod_city_id',
            'package_id',
            'form_owner_id',
            'family_code',
            'search',
            'assignment_status',
        ].forEach(function (key) {
            url.searchParams.delete(key);
        });

        if (filterParams) {
            filterParams.forEach(function (value, key) {
                if (value !== '') {
                    url.searchParams.set(key, value);
                }
            });
        }

        window.history.replaceState({}, '', url.toString());
    }

    function buildFilterParams(filterForm) {
        var params = new URLSearchParams();

        new FormData(filterForm).forEach(function (value, key) {
            if (value !== '' && !(key === 'assignment_status' && value === 'all')) {
                params.append(key, value);
            }
        });

        return params;
    }

    function setApplyingFilters(isApplying) {
        var workspace = getActiveWorkspace();

        if (!workspace) {
            return;
        }

        workspace.querySelectorAll('[data-workspace-apply-filters]').forEach(function (button) {
            button.disabled = isApplying;
            button.textContent = isApplying ? 'Applying…' : 'Apply';
        });
    }

    function resetFilterForm(filterForm) {
        filterForm.querySelectorAll('input[type="text"]').forEach(function (input) {
            input.value = '';
        });

        filterForm.querySelectorAll('select').forEach(function (select) {
            select.value = select.name === 'assignment_status' ? 'all' : '';

            if (window.AdminForm) {
                window.AdminForm.syncTomSelect(select);
            }
        });
    }

    function parseJsonResponse(response) {
        var contentType = response.headers.get('content-type') || '';

        if (contentType.indexOf('application/json') === -1) {
            throw new Error('Could not refresh hujaj results. Please refresh the page and try again.');
        }

        return response.json();
    }

    function updateResultsCount(count) {
        var workspace = getActiveWorkspace();

        if (!workspace) {
            return;
        }

        workspace.querySelectorAll('[data-flight-results-count]').forEach(function (label) {
            label.textContent = Number(count).toLocaleString() + ' rows';
        });
    }

    function loadResults(filterForm) {
        var workspace = filterForm.closest('.flight-assignment-workspace');
        var resultsContainer = getResultsContainer();
        var resultsUrl = workspace ? workspace.getAttribute('data-results-url') : null;
        var flightId = workspace ? workspace.getAttribute('data-flight-id') : null;

        if (!workspace || !resultsContainer || !resultsUrl || !flightId) {
            return;
        }

        setApplyingFilters(true);
        destroyResultsTable();
        resultsContainer.innerHTML = '<div class="text-muted py-4 text-center">Loading hujaj…</div>';

        var params = buildFilterParams(filterForm);
        var requestUrl = resultsUrl + (params.toString() ? '?' + params.toString() : '');

        fetch(requestUrl, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        })
            .then(function (response) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error('You do not have permission to manage flight assignments.');
                }

                if (!response.ok) {
                    throw new Error('Could not refresh hujaj results.');
                }

                return parseJsonResponse(response);
            })
            .then(function (data) {
                if (!data || !data.html) {
                    throw new Error('Could not refresh hujaj results.');
                }

                resultsContainer.innerHTML = data.html;
                initResultsTable();
                initBulkFormHandlers();
                updateResultsCount(typeof data.count === 'number' ? data.count : 0);
                updateBrowserUrl(flightId, params);
            })
            .catch(function (error) {
                resultsContainer.innerHTML =
                    '<div class="alert alert-danger mb-0">' +
                    (error.message || 'Could not refresh hujaj results. Please try again.') +
                    '</div>';
            })
            .finally(function () {
                setApplyingFilters(false);
            });
    }

    function loadWorkspace(url, flightId) {
        workspaceContainer.innerHTML = '<div class="text-muted py-4 text-center">Loading…</div>';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'text/html',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load assignment workspace.');
                }

                return response.text();
            })
            .then(function (html) {
                workspaceContainer.innerHTML = html;
                updateSelectedFlightRow(flightId);
                updateBrowserUrl(flightId, null);
                initBulkFormHandlers();
                initResultsTable();
                initWorkspaceFilterForms();
                initWorkspaceSelects();
            })
            .catch(function () {
                workspaceContainer.innerHTML = '<div class="alert alert-danger mb-0">Could not load assignment workspace. Please try again.</div>';
            });
    }

    function initBulkFormHandlers() {
        var form = workspaceContainer.querySelector('#flight-assignment-bulk-form');
        if (!form) {
            return;
        }

        var bulkBar = form.querySelector('#flight-assignment-bulk-bar');
        var actionInput = form.querySelector('#bulk-action-input');
        var selectAllInput = form.querySelector('.select-all-input');
        var selectAllCheckbox = form.querySelector('.select-all-checkbox');
        var pilgrimCheckboxes = form.querySelectorAll('.pilgrim-checkbox');
        var assignButton = form.querySelector('[data-bulk-action="assign"]');
        var removeButton = form.querySelector('[data-bulk-action="remove"]');
        var selectedCountLabel = form.querySelector('#flight-assignment-selected-count');

        function checkedBoxes() {
            return Array.prototype.filter.call(pilgrimCheckboxes, function (checkbox) {
                return checkbox.checked;
            });
        }

        function assignableCount() {
            if (selectAllInput && selectAllInput.value === '1') {
                return Array.prototype.filter.call(pilgrimCheckboxes, function (checkbox) {
                    return checkbox.getAttribute('data-can-assign') === '1';
                }).length;
            }

            return checkedBoxes().filter(function (checkbox) {
                return checkbox.getAttribute('data-can-assign') === '1';
            }).length;
        }

        function removableCount() {
            if (selectAllInput && selectAllInput.value === '1') {
                return Array.prototype.filter.call(pilgrimCheckboxes, function (checkbox) {
                    return checkbox.getAttribute('data-can-remove') === '1';
                }).length;
            }

            return checkedBoxes().filter(function (checkbox) {
                return checkbox.getAttribute('data-can-remove') === '1';
            }).length;
        }

        function syncSubmitState() {
            var assignCount = assignableCount();
            var removeCount = removableCount();
            var hasSelection = assignCount > 0 || removeCount > 0;
            var summaryParts = [];

            if (assignCount > 0) {
                summaryParts.push(assignCount + ' to assign');
            }

            if (removeCount > 0) {
                summaryParts.push(removeCount + ' to remove');
            }

            if (bulkBar) {
                bulkBar.classList.toggle('d-none', !hasSelection);
                bulkBar.classList.toggle('d-flex', hasSelection);
            }

            if (selectedCountLabel) {
                selectedCountLabel.textContent = hasSelection ? summaryParts.join(' · ') : '0 selected';
            }

            if (assignButton) {
                assignButton.disabled = assignCount === 0;
            }

            if (removeButton) {
                removeButton.disabled = removeCount === 0;
            }
        }

        pilgrimCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (selectAllInput && selectAllInput.value === '1') {
                    selectAllInput.value = '0';
                }

                if (selectAllCheckbox) {
                    var checkedCount = checkedBoxes().length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < pilgrimCheckboxes.length;
                    selectAllCheckbox.checked = checkedCount === pilgrimCheckboxes.length && pilgrimCheckboxes.length > 0;
                }

                syncSubmitState();
            });
        });

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                var checked = selectAllCheckbox.checked;

                pilgrimCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = checked;
                });

                if (selectAllInput) {
                    selectAllInput.value = checked ? '1' : '0';
                }

                selectAllCheckbox.indeterminate = false;
                syncSubmitState();
            });
        }

        [assignButton, removeButton].forEach(function (button) {
            if (!button) {
                return;
            }

            button.addEventListener('click', function () {
                if (actionInput) {
                    actionInput.value = button.getAttribute('data-bulk-action') || 'assign';
                }
            });
        });

        form.addEventListener('submit', function (event) {
            var action = actionInput ? actionInput.value : 'assign';
            var selectAllActive = selectAllInput && selectAllInput.value === '1';
            var count = action === 'assign' ? assignableCount() : removableCount();

            if (count === 0) {
                event.preventDefault();

                return;
            }

            form.querySelectorAll('input[name="pilgrim_ids[]"]').forEach(function (input) {
                input.remove();
            });

            if (!selectAllActive) {
                checkedBoxes().forEach(function (checkbox) {
                    var canPerform = action === 'assign'
                        ? checkbox.getAttribute('data-can-assign') === '1'
                        : checkbox.getAttribute('data-can-remove') === '1';

                    if (!canPerform) {
                        return;
                    }

                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'pilgrim_ids[]';
                    hidden.value = checkbox.value;
                    form.appendChild(hidden);
                });
            }

            var noun = count === 1 ? 'hajji' : 'hujaj';

            if (!window.confirm('Are you sure you want to ' + action + ' ' + count + ' ' + noun + '?')) {
                event.preventDefault();
                form.querySelectorAll('input[name="pilgrim_ids[]"]').forEach(function (input) {
                    input.remove();
                });
            }
        });

        syncSubmitState();
    }

    function initWorkspaceSelects() {
        if (!workspaceContainer || !window.AdminForm || !window.AdminForm.initFormSelects) {
            return;
        }

        window.AdminForm.initFormSelects(workspaceContainer);
    }

    function initWorkspaceFilterForms() {
        workspaceContainer.querySelectorAll('[data-workspace-filter-form]').forEach(function (filterForm) {
            filterForm.addEventListener('submit', function (event) {
                event.preventDefault();
                loadResults(filterForm);
            });

            var clearButton = filterForm.querySelector('[data-workspace-clear-filters]');
            if (clearButton) {
                clearButton.addEventListener('click', function () {
                    resetFilterForm(filterForm);
                    loadResults(filterForm);
                });
            }
        });
    }

    page.querySelectorAll('[data-assign-flight]').forEach(function (button) {
        button.addEventListener('click', function () {
            var flightId = button.getAttribute('data-flight-id');
            var url = button.getAttribute('data-workspace-url');

            if (!flightId || !url) {
                return;
            }

            loadWorkspace(url, flightId);
        });
    });

    initBulkFormHandlers();
    initResultsTable();
    initWorkspaceFilterForms();
    initWorkspaceSelects();
})(jQuery);
