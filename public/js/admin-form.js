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

        if (selectedValue && visibleOptions.some(function (option) {
            return option.value === selectedValue;
        })) {
            tomSelect.setValue(selectedValue, true);
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

        root.querySelectorAll('select.js-searchable-select').forEach(function (select) {
            if (select.tomselect) {
                return;
            }

            prepareSearchableSelect(select);

            new TomSelect(select, {
                plugins: ['dropdown_input'],
                allowEmptyOption: true,
                create: false,
                maxOptions: null,
                placeholder: select.dataset.placeholder || 'Select',
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
        });
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
            var existingUrl = field.dataset.existingUrl || '';
            var objectUrl = null;

            function revokeObjectUrl() {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            }

            function showEmpty() {
                emptyState.hidden = false;
                previewState.hidden = true;
                image.removeAttribute('src');
            }

            function showPreview(url) {
                image.src = url;
                emptyState.hidden = true;
                previewState.hidden = false;
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
                showPreview(objectUrl);
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

            var selectAll = section.querySelector('[data-role-permissions-select-all]');

            if (!selectAll) {
                return;
            }

            section.dataset.rolePermissionsInit = '1';

            function permissionCheckboxes() {
                return section.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
            }

            function syncSelectAllState() {
                var checkboxes = permissionCheckboxes();
                var checkedCount = Array.from(checkboxes).filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }

            selectAll.addEventListener('change', function () {
                permissionCheckboxes().forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
                selectAll.indeterminate = false;
            });

            section.addEventListener('change', function (event) {
                if (event.target.matches('input[type="checkbox"][name="permissions[]"]')) {
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
