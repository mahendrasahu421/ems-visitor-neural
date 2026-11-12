@php
    $tableId = $tableId ?? '';
    $ajaxUrl = $ajaxUrl ?? null;
    $columns = $columns ?? [];
    $order = $order ?? [[0, 'asc']];
    $columnDefs = $columnDefs ?? [];
@endphp

@push('after_scripts')
    @once
        <style>
            .datatable-card .dataTables_wrapper,
            .datatable-card .dt-container {
                width: 100%;
            }

            .datatable-card .dataTables_length label,
            .datatable-card .dataTables_filter label {
                color: #5d6778;
                font-size: 14px;
                font-weight: 500;
                margin-bottom: 0;
            }

            .datatable-card .dataTables_length select {
                min-width: 72px;
                margin: 0 6px;
            }

            .datatable-card .dataTables_filter input {
                min-width: 220px;
                margin-left: 8px;
            }

            .datatable-card table.dataTable {
                width: 100% !important;
                margin: 0 !important;
            }

            .datatable-card table.dataTable thead th,
            .datatable-card table.dataTable tbody td {
                vertical-align: middle;
            }

            .datatable-card .dataTables_info {
                color: #5d6778;
                font-size: 14px;
                padding-top: 0 !important;
            }

            .datatable-card .dataTables_paginate {
                display: flex;
                justify-content: flex-end;
            }

            .datatable-card .dataTables_paginate .paginate_button {
                border: 1px solid #e4e7ec !important;
                border-radius: 6px !important;
                color: #344054 !important;
                margin-left: 6px !important;
                padding: 7px 12px !important;
                text-decoration: none !important;
            }

            .datatable-card .dataTables_paginate .paginate_button.current,
            .datatable-card .dataTables_paginate .paginate_button.current:hover {
                background: var(--bs-primary) !important;
                border-color: var(--bs-primary) !important;
                color: #fff !important;
            }

            .datatable-card .dataTables_paginate .paginate_button.disabled {
                background: #f8f9fb !important;
                color: #98a2b3 !important;
                cursor: not-allowed !important;
            }

            @media (max-width: 767.98px) {
                .datatable-card .dataTables_filter,
                .datatable-card .dataTables_paginate {
                    justify-content: flex-start;
                    text-align: left;
                }

                .datatable-card .dataTables_filter input {
                    display: block;
                    margin: 8px 0 0;
                    min-width: 100%;
                }
            }
        </style>
    @endonce

    <script>
        $(document).ready(function() {
            const table = $('#{{ $tableId }}');

            if (!table.length || $.fn.DataTable.isDataTable(table)) {
                return;
            }

            table.closest('.card').addClass('datatable-card');
            table.css('width', '100%');

            const dataTable = table.DataTable({
                responsive: true,
                processing: true,
                serverSide: {{ $ajaxUrl ? 'true' : 'false' }},
                @if ($ajaxUrl)
                    ajax: '{{ $ajaxUrl }}',
                    columns: @json($columns),
                @endif
                scrollX: false,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                pagingType: 'simple_numbers',
                dom: "<'row align-items-center g-3 px-3 pt-3 pb-2'<'col-md-6'l><'col-md-6'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center g-3 px-3 py-3'<'col-md-5'i><'col-md-7'p>>",
                order: @json($order),
                columnDefs: @json($columnDefs),
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    emptyTable: 'No data available',
                    zeroRecords: 'No matching records found'
                },
                initComplete: function() {
                    polishDataTable(table);
                },
                drawCallback: function() {
                    polishDataTable(table);
                }
            });

            dataTable.columns.adjust();
        });

        function polishDataTable(table) {
            const wrapper = table.closest('.dataTables_wrapper, .dt-container');

            wrapper.find('.dataTables_length select').addClass('form-select form-select-sm d-inline-block');
            wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm d-inline-block');
            wrapper.find('.dataTables_paginate').addClass('mt-0');
            wrapper.find('.pagination').addClass('mb-0 justify-content-end');
        }
    </script>
@endpush
