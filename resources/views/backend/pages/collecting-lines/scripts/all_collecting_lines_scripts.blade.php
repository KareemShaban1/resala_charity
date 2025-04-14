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
                    data: 'all_collected',
                    name: 'all_collected'
                },
                {
                    data: 'status',
                    name: 'status'
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


        $('#date-filter').on('change', function() {
            if ($(this).val() === 'range') {
                $('#custom-range, #end-date-container').show();
            } else {
                $('#custom-range, #end-date-container').hide();
                $('#start-date, #end-date').val('');
            }
            allCollectingLinesTable.ajax.reload();
        });

        $('#start-date, #end-date').on('change', function() {
            allCollectingLinesTable.ajax.reload();
        });

        $('#clear-filters').on('click', function() {
            $('#date-filter').val('all').trigger('change');
            $('#start-date, #end-date').val('');
            allCollectingLinesTable.ajax.reload();
        });

        // Open the View Donations Modal when the "View Donations" button is clicked
        $('#all-collecting-lines-table').on('click', '.view-donations-btn', function() {
            var collectingLineId = $(this).data('id'); // Ensure this matches the button's data attribute
            // Store the collectingLine ID in the modal's data attribute
            $('#viewDonationsModal').data('collecting-line-id', collectingLineId);

            // Show the modal
            $('#viewDonationsModal').modal('show');

            // Initialize DataTable for viewing donations (only if not already initialized)
            if (!$.fn.DataTable.isDataTable('#view-donations-table')) {
                var viewDonationsTable = $('#view-donations-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('collecting-lines.donations.data') }}",
                        data: function(d) {
                            // Retrieve the collectingLine ID from the modal's data attribute
                            d.collecting_line_id = $('#viewDonationsModal').data('collecting-line-id');
                            // Additional filters (if needed)
                            d.status = $('#status-filter').val();

                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'area',
                            name: 'area'
                        },
                        {
                            data: 'phones',
                            name: 'phones',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'monthly_donation_day',
                            name: 'monthly_donation_day'
                        },
                        {
                            data: 'collected',
                            name: 'collected'
                        },
                        {
                            data: 'donateItems',
                            name: 'donateItems',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'collecting_line_id',
                            name: 'collecting_line_id',
                            orderable: false,
                            searchable: false,
                            visible: false
                        },
                        {
                            data: 'un_assign_actions',
                            name: 'un_assign_actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    buttons: [{
                            text: 'Bulk Un Assign',
                            action: function() {
                                unAssignBulkDonations();
                            }
                        }, {
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
            } else {
                // If the DataTable is already initialized, reload it with the new collecting_line_id
                $('#view-donations-table').DataTable().ajax.reload();
            }
        });

        // Clear the collectingLine ID when the modal is hidden
        $('#viewDonationsModal').on('hidden.bs.modal', function() {
            $(this).removeData('collecting-line-id');
        });

        // When "select all" is clicked
        $('#select-all').on('click', function() {
            $('.row-checkbox').prop('checked', this.checked);
        });

        // Optional: if any row is unchecked, uncheck "select all"
        $(document).on('change', '.row-checkbox', function() {
            if (!this.checked) {
                $('#select-all').prop('checked', false);
            }

            // If all checkboxes are checked, check "select all"
            if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                $('#select-all').prop('checked', true);
            }
        });

        function unAssignBulkDonations() {
            let selectedDonations = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            console.log(selectedDonations);

            if (selectedDonations.length === 0) {
                alert('Please select at least one item');
                return;
            }

            let collectingLineId = $('#viewDonationsModal').data('collecting-line-id');

            // if (!confirm('Are you sure you want to unassign selected donations?')) return;

            $.ajax({
                url: "{{ route('collecting-lines.un-assign-bulk-donation') }}",
                method: 'POST',
                data: {
                    donation_ids: selectedDonations,
                    collecting_line_id: collectingLineId,
                    _token: $('meta[name="csrf-token"]').attr('content') // if CSRF is needed
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#view-donations-table').DataTable().ajax.reload();
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        };

        // Function to format date
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        // Edit Collecting Line
        $('#all-collecting-lines-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_representative_id').val($(this).data('representative-id'));
            $('#edit_driver_id').val($(this).data('driver-id'));
            $('#edit_employee_id').val($(this).data('employee-id'));
            $('#edit_area_group_id').val($(this).data('area-group-id'));
            $('#edit_collecting_date').val(formatDate($(this).data('collecting-date')));
            $('#edit_status').val($(this).data('status'));
            $('#editCollectingLineModal').modal('show');
        });

        $('#editCollectingLineForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_id').val();
            $.ajax({
                url: "/collecting-lines/" + id,
                type: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#editCollectingLineModal').modal('hide');
                    allCollectingLinesTable.ajax.reload();
                }
            });
        });


        // Delete Collecting Line
        $('#all-collecting-lines-table').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            $('#delete_id').val(id);
            $('#deleteCollectingLineModal').modal('show');
        });

        $('#deleteCollectingLineForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#delete_id').val();
            $.ajax({
                url: "/collecting-lines/" + id,
                type: 'DELETE',
                data: $(this).serialize(),
                success: function(response) {
                    $('#deleteCollectingLineModal').modal('hide');
                    allCollectingLinesTable.ajax.reload();
                }
            });
        });

    });
</script>

@endpush