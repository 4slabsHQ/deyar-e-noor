(function ($) {
    'use strict';

    var page = document.getElementById('reports-page');
    var form = document.getElementById('report-builder-form');

    if (!page || !form) {
        return;
    }

    var resultsContainer = document.getElementById('report-results');
    var validationAlert = document.getElementById('report-validation-alert');
    var resultsUrl = page.getAttribute('data-results-url');
    var generateButton = form.querySelector('[data-report-generate]');
    var checkboxes = form.querySelectorAll('.report-column-checkbox');
    var saveForm = document.getElementById('report-save-columns-form');
    var saveInputs = document.getElementById('report-save-columns-inputs');
    var titleModalElement = document.getElementById('report-title-modal');
    var titleInput = document.getElementById('report-title-input');
    var titleConfirmButton = document.getElementById('report-title-confirm');
    var titleModalInstance = null;
    var pendingTitleAction = null;

    function getTitleModal() {
        if (!titleModalElement || !window.bootstrap || !window.bootstrap.Modal) {
            return null;
        }

        if (titleModalInstance) {
            return titleModalInstance;
        }

        if (typeof window.bootstrap.Modal.getOrCreateInstance === 'function') {
            titleModalInstance = window.bootstrap.Modal.getOrCreateInstance(titleModalElement);
        } else {
            titleModalInstance = new window.bootstrap.Modal(titleModalElement);
        }

        return titleModalInstance;
    }

    function defaultReportTitle() {
        var card = resultsContainer ? resultsContainer.querySelector('[data-default-report-title]') : null;

        if (!card) {
            return 'Report';
        }

        return card.getAttribute('data-default-report-title') || 'Report';
    }

    function askReportTitle(callback) {
        var titleModal = getTitleModal();

        if (!titleModal || !titleInput || !titleConfirmButton) {
            var fallbackTitle = window.prompt('Report title:', defaultReportTitle());

            if (fallbackTitle === null) {
                return;
            }

            fallbackTitle = fallbackTitle.trim();

            if (fallbackTitle === '') {
                showValidationAlert('Enter a report title.');
                return;
            }

            callback(fallbackTitle);

            return;
        }

        titleInput.value = defaultReportTitle();
        pendingTitleAction = callback;
        titleModal.show();
        window.setTimeout(function () {
            titleInput.focus();
            titleInput.select();
        }, 150);
    }

    if (titleConfirmButton) {
        titleConfirmButton.addEventListener('click', function () {
            if (!titleInput) {
                return;
            }

            var title = titleInput.value.trim();

            if (title === '') {
                showValidationAlert('Enter a report title.');
                return;
            }

            var titleModal = getTitleModal();

            if (titleModal) {
                titleModal.hide();
            }

            if (pendingTitleAction) {
                pendingTitleAction(title);
                pendingTitleAction = null;
            }
        });
    }

    if (titleInput) {
        titleInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();

                if (titleConfirmButton) {
                    titleConfirmButton.click();
                }
            }
        });
    }

    function selectedValues() {
        return Array.prototype.map.call(selectedCheckboxes(), function (checkbox) {
            return checkbox.value;
        });
    }

    function selectedCheckboxes() {
        return Array.prototype.filter.call(checkboxes, function (checkbox) {
            return checkbox.checked;
        });
    }

    function setAll(checked) {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    }

    function buildQueryParams() {
        var params = new URLSearchParams();

        new FormData(form).forEach(function (value, key) {
            if (key === 'columns[]') {
                return;
            }

            if (value !== '') {
                params.append(key, value);
            }
        });

        selectedValues().forEach(function (column) {
            params.append('columns[]', column);
        });

        params.set('run', '1');

        return params;
    }

    function updateBrowserUrl(params) {
        var url = new URL(form.getAttribute('action'), window.location.origin);

        params.forEach(function (value, key) {
            url.searchParams.append(key, value);
        });

        window.history.replaceState({}, '', url.toString());
    }

    function clearValidationAlert() {
        if (validationAlert) {
            validationAlert.innerHTML = '';
        }
    }

    function showValidationAlert(message) {
        if (!validationAlert) {
            window.alert(message);
            return;
        }

        validationAlert.innerHTML =
            '<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
    }

    function destroyResultsTable() {
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
        if (!resultsContainer || !window.deyarInitDataTable || !$) {
            return;
        }

        $(resultsContainer).find('table[data-datatable]').each(function () {
            window.deyarInitDataTable($(this));
        });
    }

    function setGenerating(isGenerating) {
        if (!generateButton) {
            return;
        }

        generateButton.disabled = isGenerating;
        generateButton.textContent = isGenerating ? 'Generating…' : 'Generate';
    }

    function parseJsonResponse(response) {
        var contentType = response.headers.get('content-type') || '';

        if (contentType.indexOf('application/json') === -1) {
            throw new Error('Could not generate the report. Please refresh the page and try again.');
        }

        return response.json();
    }

    function loadResults() {
        if (!resultsUrl || !resultsContainer) {
            form.submit();
            return;
        }

        clearValidationAlert();
        setGenerating(true);
        destroyResultsTable();
        resultsContainer.innerHTML = '<div class="text-muted py-4 text-center">Loading results…</div>';

        var params = buildQueryParams();

        fetch(resultsUrl + '?' + params.toString(), {
            credentials: 'same-origin',
            redirect: 'manual',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        })
            .then(function (response) {
                if (response.status === 422) {
                    return parseJsonResponse(response).then(function (data) {
                        var messages = Object.values(data.errors || {}).flat().join(' ');
                        throw new Error(messages || data.message || 'Could not generate the report.');
                    });
                }

                if (response.type === 'opaqueredirect' || response.status === 301 || response.status === 302) {
                    throw new Error('Your session may have expired. Please refresh the page and try again.');
                }

                if (response.status === 401 || response.status === 403) {
                    throw new Error('You do not have permission to generate this report.');
                }

                if (!response.ok) {
                    throw new Error('Could not generate the report.');
                }

                return parseJsonResponse(response);
            })
            .then(function (data) {
                if (!data || !data.html) {
                    throw new Error('Could not generate the report.');
                }

                resultsContainer.innerHTML = data.html;
                initResultsTable();
                updateBrowserUrl(params);
                resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function (error) {
                resultsContainer.innerHTML = '';
                showValidationAlert(error.message || 'Could not generate the report.');
            })
            .finally(function () {
                setGenerating(false);
            });
    }

    var selectAllButton = document.querySelector('[data-report-select-all-columns]');
    var clearButton = document.querySelector('[data-report-clear-columns]');
    var saveDefaultsButton = document.querySelector('[data-report-save-defaults]');

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function () {
            setAll(true);
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', function () {
            setAll(false);
        });
    }

    if (saveDefaultsButton && saveForm && saveInputs) {
        saveDefaultsButton.addEventListener('click', function () {
            var selected = selectedValues();

            if (selected.length === 0) {
                window.alert('Select at least one column to save as default.');
                return;
            }

            saveInputs.innerHTML = '';

            selected.forEach(function (value) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'columns[]';
                input.value = value;
                saveInputs.appendChild(input);
            });

            saveForm.submit();
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function buildPrintHtml(title, meta, headings, rows) {
        var landscape = headings.length > 6;
        var headHtml = headings.map(function (heading) {
            return '<th>' + escapeHtml(heading) + '</th>';
        }).join('');
        var bodyHtml = rows.map(function (row) {
            return '<tr>' + row.map(function (cell) {
                return '<td>' + escapeHtml(cell || '—') + '</td>';
            }).join('') + '</tr>';
        }).join('');

        return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>'
            + escapeHtml(title)
            + '</title><style>'
            + '@page { size: '
            + (landscape ? 'A4 landscape' : 'A4 portrait')
            + '; margin: 12mm; }'
            + 'body { font-family: Arial, sans-serif; color: #111827; margin: 0; }'
            + 'h1 { font-size: 16px; margin: 0 0 4px; }'
            + '.meta { color: #6b7280; font-size: 11px; margin: 0 0 12px; }'
            + 'table { width: 100%; border-collapse: collapse; table-layout: fixed; }'
            + 'th, td { border: 1px solid #d1d5db; padding: 4px 5px; font-size: 9px; line-height: 1.35; vertical-align: top; word-break: break-word; overflow-wrap: anywhere; }'
            + 'th { background: #f3f4f6; font-weight: 600; }'
            + 'tr:nth-child(even) td { background: #fafafa; }'
            + '</style></head><body><h1>'
            + escapeHtml(title)
            + '</h1><p class="meta">'
            + escapeHtml(meta)
            + '</p><table><thead><tr>'
            + headHtml
            + '</tr></thead><tbody>'
            + bodyHtml
            + '</tbody></table></body></html>';
    }

    function collectPrintData() {
        var dataEl = resultsContainer ? resultsContainer.querySelector('.report-print-data') : null;

        if (!dataEl) {
            return null;
        }

        try {
            return JSON.parse(dataEl.textContent);
        } catch (error) {
            return null;
        }
    }

    function printHtml(html) {
        var frame = document.getElementById('report-print-frame');

        if (!frame) {
            frame = document.createElement('iframe');
            frame.id = 'report-print-frame';
            frame.setAttribute('aria-hidden', 'true');
            frame.style.cssText = 'position:absolute;width:0;height:0;border:0;clip:rect(0 0 0 0);overflow:hidden;';
            document.body.appendChild(frame);
        }

        var frameWindow = frame.contentWindow;
        var doc = frameWindow.document;

        doc.open();
        doc.write(html);
        doc.close();

        setTimeout(function () {
            frameWindow.focus();
            frameWindow.print();
        }, 300);
    }

    function printResults() {
        if (!resultsContainer) {
            return;
        }

        var printData = collectPrintData();

        if (!printData || !printData.headings || !printData.rows) {
            showValidationAlert('Generate the report before printing.');
            return;
        }

        askReportTitle(function (title) {
            try {
                printHtml(buildPrintHtml(
                    title,
                    printData.meta || '',
                    printData.headings,
                    printData.rows
                ));
            } catch (error) {
                showValidationAlert('Could not open the print view.');
            }
        });
    }

    page.addEventListener('click', function (event) {
        var exportLink = event.target.closest('[data-report-export]');

        if (exportLink) {
            event.preventDefault();

            askReportTitle(function (title) {
                var url = new URL(exportLink.getAttribute('href'), window.location.origin);
                url.searchParams.set('report_title', title);
                window.location.href = url.toString();
            });

            return;
        }

        var printButton = event.target.closest('[data-report-print]');

        if (!printButton) {
            return;
        }

        event.preventDefault();
        printResults();
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (selectedCheckboxes().length === 0) {
            showValidationAlert('Select at least one column.');
            return;
        }

        loadResults();
    });

    initResultsTable();
})(window.jQuery);
