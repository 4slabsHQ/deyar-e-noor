(function ($) {
    'use strict';

    function buildDataTableOptions($table) {
        var options = {
            autoWidth: false,
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
        };

        if ($table.data('scrollX') || $table.data('scroll-x')) {
            options.scrollX = true;
        }

        if ($table.hasClass('report-results-table')) {
            options.scrollX = true;
            options.autoWidth = true;
            options.paging = false;
            options.lengthChange = false;
            options.info = false;
            options.columnDefs = options.columnDefs.concat([
                { orderable: false, targets: 0, className: 'report-serial-column' },
            ]);
        }
        if ($table.hasClass('flight-assignment-hujaj-table')) {
            options.paging = false;
            options.lengthChange = false;
            options.info = false;
            options.columnDefs = options.columnDefs.concat([
                { orderable: false, targets: 0, className: 'assignment-check-col' },
                { orderable: false, targets: 1, className: 'flight-serial-column' },
                { width: '2.75rem', targets: 0 },
                { width: '3.5rem', targets: 1 },
                { width: '14%', targets: 2 },
                { width: '9%', targets: 3 },
                { width: '8%', targets: 4 },
                { width: '13%', targets: 5 },
                { width: '10%', targets: 6 },
                { width: '12%', targets: 7 },
                { width: '10.5rem', targets: 8 },
            ]);
        }

        if ($table.hasClass('flight-assignment-flights-table')) {
            options.columnDefs = options.columnDefs.concat([
                { width: '11%', targets: 0 },
                { width: '7%', targets: 1 },
                { width: '14%', targets: 2 },
                { width: '8%', targets: 3 },
                { width: '14%', targets: 4 },
                { width: '11%', targets: 5 },
                { width: '8%', targets: 6 },
                { width: '6%', targets: 7 },
                { width: '8%', targets: 8 },
                { width: '5.5rem', targets: 9 },
            ]);
        }

        return options;
    }

    window.deyarInitDataTable = function ($table) {
        if (!$table.length || $.fn.DataTable.isDataTable($table)) {
            return;
        }

        $table.addClass('admin-index-table');
        $table.DataTable(buildDataTableOptions($table));
    };

    $(function () {
        $('table[data-datatable]').each(function () {
            window.deyarInitDataTable($(this));
        });
    });
})(jQuery);
