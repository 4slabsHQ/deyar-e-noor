(function () {
    'use strict';

    function syncSelectPlaceholder(select) {
        var empty = select.value === '' || select.value === null;

        select.classList.toggle('is-placeholder', empty);
    }

    function initSelectPlaceholders(root) {
        root.querySelectorAll('select.form-control').forEach(function (select) {
            if (select.tomselect) {
                return;
            }

            syncSelectPlaceholder(select);
            select.addEventListener('change', function () {
                syncSelectPlaceholder(select);
            });
        });
    }

    function refreshTomSelectOptions(select, visibleOptions, selectedValue) {
        if (!select.tomselect) {
            return;
        }

        var tomSelect = select.tomselect;

        tomSelect.clear(true);
        tomSelect.clearOptions();

        visibleOptions.forEach(function (option) {
            tomSelect.addOption({ value: option.value, text: option.textContent.trim() });
        });

        tomSelect.refreshOptions(false);

        var matchingOption = visibleOptions.find(function (option) {
            return option.value === selectedValue;
        });

        if (matchingOption) {
            tomSelect.setValue(selectedValue, true);
        } else if (visibleOptions.some(function (option) {
            return option.value === '';
        })) {
            tomSelect.setValue('', true);
        } else {
            tomSelect.clear(true);
        }
    }

    function prepareSearchableSelect(select) {
        var hasValue = select.value !== '';
        var placeholder = (select.dataset.placeholder || 'Select').trim();

        select.querySelectorAll('option[value=""][disabled]').forEach(function (option) {
            option.remove();
        });

        var hasBlankEmptyOption = false;

        select.querySelectorAll('option[value=""]').forEach(function (option) {
            var label = option.textContent.trim();

            if (select.dataset.keepEmptyLabel === 'true' || select.classList.contains('js-filter-select')) {
                return;
            }

            if (label === '' || label === placeholder || label === 'Select') {
                option.textContent = '';
                option.disabled = false;
                hasBlankEmptyOption = true;

                if (!hasValue) {
                    option.selected = true;
                }
            }
        });

        if (!hasBlankEmptyOption && !select.querySelector('option[value=""]')) {
            var emptyOption = document.createElement('option');
            emptyOption.value = '';

            if (!hasValue) {
                emptyOption.selected = true;
            }

            select.insertBefore(emptyOption, select.firstChild);
        }
    }

    function initSearchableSelects(root) {
        if (typeof TomSelect === 'undefined') {
            return;
        }

        root.querySelectorAll('select.js-searchable-select:not([data-defer-tom-select])').forEach(function (select) {
            initSingleSearchableSelect(select);
        });
    }

    function initSingleSearchableSelect(select) {
        if (!select || select.tomselect || typeof TomSelect === 'undefined') {
            return;
        }

        prepareSearchableSelect(select);

        var dropdownParent = select.dataset.dropdownParent || null;

        new TomSelect(select, {
            plugins: ['dropdown_input'],
            allowEmptyOption: true,
            create: false,
            maxOptions: null,
            placeholder: select.dataset.placeholder || 'Select',
            dropdownParent: dropdownParent,
            onInitialize: function () {
                this.wrapper.classList.remove('form-control', 'js-searchable-select');
            },
            onDropdownOpen: function () {
                this.setTextboxValue('');
            },
            onChange: function () {
                syncSelectPlaceholder(select);
            },
        });

        syncSelectPlaceholder(select);
    }

    function initCountryCityCascade(root) {
        root.querySelectorAll('[data-country-city]').forEach(function (wrapper) {
            var countrySelect = wrapper.querySelector('[data-country-select]');
            var citySelect = wrapper.querySelector('[data-city-select]');

            if (!countrySelect || !citySelect) {
                return;
            }

            var cities = Array.from(citySelect.querySelectorAll('option[data-country-id]'));

            function visibleCities(countryId) {
                return cities.filter(function (option) {
                    return !countryId || option.dataset.countryId === countryId;
                });
            }

            function filterCities() {
                var countryId = countrySelect.value;
                var currentValue = citySelect.value;
                var visible = visibleCities(countryId);
                var selected = visible.find(function (option) {
                    return option.value === currentValue;
                });

                cities.forEach(function (option) {
                    var matches = !countryId || option.dataset.countryId === countryId;
                    option.hidden = !matches;
                });

                if (!selected) {
                    citySelect.value = '';
                }

                syncSelectPlaceholder(citySelect);
                refreshTomSelectOptions(citySelect, visible, selected ? currentValue : '');
            }

            countrySelect.addEventListener('change', filterCities);
            filterCities();
        });
    }

    function initImageUploads(root) {
        root.querySelectorAll('.admin-image-upload').forEach(function (field) {
            if (field.dataset.imageUploadInit === '1') {
                return;
            }

            field.dataset.imageUploadInit = '1';

            var input = field.querySelector('.admin-image-upload__input');
            var removeInput = field.querySelector('.js-image-remove-flag');
            var emptyState = field.querySelector('.js-image-empty');
            var previewState = field.querySelector('.js-image-preview');
            var image = field.querySelector('.js-image-preview-img');
            var filePreview = field.querySelector('.js-file-preview');
            var fileName = field.querySelector('.js-file-preview-name');
            var existingUrl = field.dataset.existingUrl || '';
            var existingFilename = field.dataset.existingFilename || '';
            var objectUrl = null;

            function isImageSource(source, filename) {
                if (source && typeof source.type === 'string') {
                    return source.type.indexOf('image/') === 0;
                }

                var name = filename || (typeof source === 'string' ? source : '');

                return /\.(jpe?g|png|gif|webp|bmp|svg)(\?|$)/i.test(name);
            }

            function revokeObjectUrl() {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            }

            function hidePreviewMedia() {
                image.hidden = true;
                image.removeAttribute('src');
                filePreview.hidden = true;

                if (fileName) {
                    fileName.textContent = '';
                }
            }

            function showEmpty() {
                emptyState.hidden = false;
                previewState.hidden = true;
                hidePreviewMedia();
            }

            function showImagePreview(url) {
                image.hidden = false;
                filePreview.hidden = true;
                image.src = url;
                emptyState.hidden = true;
                previewState.hidden = false;
            }

            function showFilePreview(url, name) {
                image.hidden = true;
                image.removeAttribute('src');
                filePreview.hidden = false;

                if (fileName) {
                    fileName.textContent = name;
                }

                emptyState.hidden = true;
                previewState.hidden = false;
            }

            function showPreview(url, file) {
                var name = file ? file.name : existingFilename || url.split('/').pop() || 'Uploaded file';

                if (file ? isImageSource(file) : isImageSource(url, name)) {
                    showImagePreview(url);

                    return;
                }

                showFilePreview(url, name);
            }

            function openFilePicker() {
                input.click();
            }

            field.querySelector('.js-image-upload')?.addEventListener('click', openFilePicker);
            field.querySelector('.js-image-change')?.addEventListener('click', openFilePicker);

            field.querySelector('.js-image-remove')?.addEventListener('click', function () {
                input.value = '';
                if (removeInput) {
                    removeInput.value = '1';
                }
                revokeObjectUrl();
                showEmpty();
            });

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];

                if (!file) {
                    if (existingUrl && (!removeInput || removeInput.value !== '1')) {
                        showPreview(existingUrl);
                    } else {
                        showEmpty();
                    }

                    return;
                }

                if (removeInput) {
                    removeInput.value = '0';
                }
                revokeObjectUrl();
                objectUrl = URL.createObjectURL(file);
                showPreview(objectUrl, file);
            });

            if (existingUrl) {
                if (removeInput) {
                    removeInput.value = '0';
                }
                showPreview(existingUrl);
            }
        });
    }

    function initRolePermissions(root) {
        root.querySelectorAll('[data-role-permissions]').forEach(function (section) {
            if (section.dataset.rolePermissionsInit === '1') {
                return;
            }

            section.dataset.rolePermissionsInit = '1';

            var selectAll = section.querySelector('[data-role-permissions-select-all]');

            function permissionCheckboxes() {
                return section.querySelectorAll('input.js-permission-checkbox[name="permissions[]"]');
            }

            function groupCheckboxes(groupKey) {
                return section.querySelectorAll('input.js-permission-checkbox[data-permission-group="' + groupKey + '"]');
            }

            function syncSelectAllState() {
                if (!selectAll) {
                    return;
                }

                var checkboxes = permissionCheckboxes();
                var checkedCount = Array.from(checkboxes).filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }

            section.addEventListener('click', function (event) {
                var groupButton = event.target.closest('[data-permission-group-select]');

                if (groupButton) {
                    event.preventDefault();

                    var groupKey = groupButton.getAttribute('data-permission-group-select');
                    var checkboxes = groupCheckboxes(groupKey);
                    var shouldCheck = !Array.from(checkboxes).every(function (checkbox) {
                        return checkbox.checked;
                    });

                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = shouldCheck;
                    });

                    syncSelectAllState();

                    return;
                }
            });

            section.addEventListener('change', function (event) {
                var target = event.target;

                if (target.matches('[data-role-permissions-select-all]')) {
                    permissionCheckboxes().forEach(function (checkbox) {
                        checkbox.checked = target.checked;
                    });
                    target.indeterminate = false;

                    return;
                }

                if (target.matches('input.js-permission-checkbox[name="permissions[]"]')) {
                    syncSelectAllState();
                }
            });

            syncSelectAllState();
        });
    }

    function initAdminForms() {
        document.querySelectorAll('.admin-form, .hajj-form, .pilgrim-form').forEach(function (formRoot) {
            initSelectPlaceholders(formRoot);
            initCountryCityCascade(formRoot);
            initSearchableSelects(formRoot);
            initImageUploads(formRoot);
            initRolePermissions(formRoot);
        });
    }

    function visibleSelectOptions(select) {
        return Array.from(select.options).filter(function (option) {
            return option.value === '' || !option.hidden;
        });
    }

    function setSelectEnabled(select, enabled) {
        if (!select) {
            return;
        }

        select.disabled = !enabled;

        if (select.tomselect) {
            if (enabled) {
                select.tomselect.enable();
            } else {
                select.tomselect.disable();
            }
        }
    }

    function syncTomSelect(select) {
        if (!select || !select.tomselect) {
            syncSelectPlaceholder(select);

            return;
        }

        refreshTomSelectOptions(select, visibleSelectOptions(select), select.value);
        syncSelectPlaceholder(select);
    }

    window.AdminForm = {
        syncTomSelect: syncTomSelect,
        syncSelectPlaceholder: syncSelectPlaceholder,
        setSelectEnabled: setSelectEnabled,
        initSearchableSelect: initSingleSearchableSelect,
        initFormSelects: function (root) {
            var scope = root || document;

            initSelectPlaceholders(scope);
            initSearchableSelects(scope);
        },
        refreshVisibleOptions: function (select) {
            refreshTomSelectOptions(select, visibleSelectOptions(select), select.value);
            syncSelectPlaceholder(select);
        },
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminForms);
    } else {
        initAdminForms();
    }
})();
