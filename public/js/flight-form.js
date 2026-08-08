(function () {
    function initFlightForm(root) {
        const airlines = JSON.parse(root.dataset.airlines || '[]');
        const indirectValue = root.dataset.indirectValue || 'indirect';

        const flightTypeSelect = root.querySelector('[data-flight-type]');
        const viaSection = root.querySelector('[data-via-section]');
        const totalStayDisplay = root.querySelector('[data-total-stay-display]');

        function airlineCode(airlineId) {
            const airline = airlines.find(function (item) {
                return String(item.id) === String(airlineId);
            });

            return airline ? airline.code : '';
        }

        function updatePrefix(prefix) {
            const airlineSelectId = prefix.dataset.airlineSelect;
            const select = airlineSelectId ? root.querySelector('#' + airlineSelectId) : null;

            prefix.textContent = select && select.value ? airlineCode(select.value) : '—';
        }

        function filterAirports(citySelect) {
            const pair = citySelect.dataset.cityAirportPair;
            const airportSelect = root.querySelector('[data-city-airport-airport][data-city-airport-pair="' + pair + '"]');

            if (!airportSelect) {
                return;
            }

            const cityId = citySelect.value;
            const currentValue = airportSelect.value;

            Array.from(airportSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;

                    return;
                }

                const matches = !cityId || option.dataset.cityId === cityId;
                option.hidden = !matches;
            });

            const selected = airportSelect.querySelector('option[value="' + currentValue + '"]:not([hidden])');

            if (!selected) {
                airportSelect.value = '';
            }

            syncSelectPlaceholder(airportSelect);

            if (window.AdminForm) {
                window.AdminForm.refreshVisibleOptions(airportSelect);
            }
        }

        function syncSelectPlaceholder(select) {
            select.classList.toggle('is-placeholder', !select.value);
        }

        function setViaFieldEnabled(field, enabled) {
            if (field.id === 'via_total_stay_display') {
                field.disabled = !enabled;

                return;
            }

            if (window.AdminForm && typeof window.AdminForm.setSelectEnabled === 'function') {
                window.AdminForm.setSelectEnabled(field, enabled);
            } else {
                field.disabled = !enabled;
            }
        }

        function toggleViaSection() {
            const isIndirect = flightTypeSelect && flightTypeSelect.value === indirectValue;

            if (viaSection) {
                viaSection.classList.toggle('is-hidden', !isIndirect);
                viaSection.querySelectorAll('input, select').forEach(function (field) {
                    setViaFieldEnabled(field, isIndirect);
                });

                if (isIndirect) {
                    root.querySelectorAll('[data-city-airport-city][data-city-airport-pair="via"]').forEach(function (citySelect) {
                        filterAirports(citySelect);
                    });
                }
            }

            if (!isIndirect && totalStayDisplay) {
                totalStayDisplay.value = '';
            } else {
                calculateTotalStay();
            }
        }

        function calculateTotalStay() {
            if (!totalStayDisplay || !flightTypeSelect || flightTypeSelect.value !== indirectValue) {
                return;
            }

            const arrivalDate = root.querySelector('#via_arrival_date')?.value;
            const arrivalTime = root.querySelector('#via_arrival_time')?.value;
            const departureDate = root.querySelector('#via_departure_date')?.value;
            const departureTime = root.querySelector('#via_departure_time')?.value;

            if (!arrivalDate || !arrivalTime || !departureDate || !departureTime) {
                totalStayDisplay.value = '';
                return;
            }

            const arrival = new Date(arrivalDate + 'T' + arrivalTime + ':00');
            const departure = new Date(departureDate + 'T' + departureTime + ':00');
            const diffMinutes = Math.floor((departure - arrival) / 60000);

            if (Number.isNaN(diffMinutes) || diffMinutes <= 0) {
                totalStayDisplay.value = '';
                return;
            }

            const days = Math.floor(diffMinutes / (60 * 24));
            const hours = Math.floor((diffMinutes % (60 * 24)) / 60);
            const minutes = diffMinutes % 60;
            const parts = [];

            if (days > 0) {
                parts.push(days + 'd');
            }

            if (hours > 0) {
                parts.push(hours + 'h');
            }

            if (minutes > 0 || parts.length === 0) {
                parts.push(minutes + 'm');
            }

            totalStayDisplay.value = parts.join(' ');
        }

        root.querySelectorAll('[data-city-airport-city]').forEach(function (citySelect) {
            citySelect.addEventListener('change', function () {
                filterAirports(citySelect);
            });
            filterAirports(citySelect);
        });

        root.querySelectorAll('[data-flight-number-prefix]').forEach(function (prefix) {
            const airlineSelectId = prefix.dataset.airlineSelect;
            const select = airlineSelectId ? root.querySelector('#' + airlineSelectId) : null;

            if (select) {
                select.addEventListener('change', function () {
                    updatePrefix(prefix);
                });
            }

            updatePrefix(prefix);
        });

        root.querySelectorAll('select.form-control').forEach(function (select) {
            if (select.tomselect) {
                return;
            }

            select.addEventListener('change', function () {
                syncSelectPlaceholder(select);
            });
            syncSelectPlaceholder(select);
        });

        if (flightTypeSelect) {
            flightTypeSelect.addEventListener('change', toggleViaSection);
            toggleViaSection();
        }

        ['via_arrival_date', 'via_arrival_time', 'via_departure_date', 'via_departure_time'].forEach(function (fieldId) {
            const field = root.querySelector('#' + fieldId);

            if (field) {
                field.addEventListener('change', calculateTotalStay);
                field.addEventListener('input', calculateTotalStay);
            }
        });

        calculateTotalStay();
    }

    document.querySelectorAll('[data-flight-form]').forEach(initFlightForm);
})();
