(function ($) {
    "use strict";

    $(function () {
        $('table[data-datatable]').each(function () {
            var $table = $(this);

            if ($.fn.DataTable.isDataTable($table)) {
                return;
            }

            $table.DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    },
                    emptyTable: $table.data('empty-message') || 'No records found.'
                },
                columnDefs: [
                    { orderable: false, targets: 'no-sort' }
                ]
            });
        });
    });

})(jQuery);
