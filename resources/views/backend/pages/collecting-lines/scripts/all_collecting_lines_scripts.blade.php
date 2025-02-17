@push('scripts')

<script>
        $(document).ready(function() {

      // Initialize DataTables
      allCollectingLinesTable = $('#all-collecting-lines-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('collecting-lines.data') }}",
                data: function(d) {
                    // d.date = $('#date').val();
                        d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.area_group = $('#area_group').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'number',
                    name: 'number',
                    render: function(data, type, row) {
                        return `
                            <a href="/collecting-lines/${row.id}/show" class="text-info">
                                ${row.number}
                            </a>
                        `;
                    }

                },
                {
                    data: 'areaGroup',
                    name: 'areaGroup'
                },
                {
                    data: 'representative',
                    name: 'representative'
                },
                {
                    data: 'driver',
                    name: 'driver'
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ],
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Areas Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Areas Data', 
                //     exportOptions: {
                //         columns: [0, 1, 2, 3]
                //     }
                // },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language], // Apply language dynamically
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });


        $('#date-filter').on('change', function () {
        if ($(this).val() === 'range') {
            $('#custom-range, #end-date-container').show();
        } else {
            $('#custom-range, #end-date-container').hide();
            $('#start-date, #end-date').val('');
        }
        allCollectingLinesTable.ajax.reload();
    });

    $('#start-date, #end-date').on('change', function () {
        allCollectingLinesTable.ajax.reload();
    });

    $('#clear-filters').on('click', function() {
        $('#date-filter').val('all').trigger('change');
        $('#start-date, #end-date').val('');
        allCollectingLinesTable.ajax.reload();
    });
});

</script>

@endpush