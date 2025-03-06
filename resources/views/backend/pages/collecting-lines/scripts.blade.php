@push('scripts')
<script>
            let donationsTable;
            let collectingLinesTable;
            let monthlyFormstable;

    $(document).ready(function() {
        // Set default date to today
        var today = new Date().toISOString().split('T')[0];
        $('#date').val(today);

        // Initialize DataTables
        collectingLinesTable = $('#collecting-lines-table').DataTable({
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
            lengthMenu: [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]], 
            pageLength: 10,
            responsive: true,
            language: languages[language], // Apply language dynamically
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });


        donationsTable = $('#donations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('collecting-lines.donations') }}",
                data: function(d) {
                    // d.date = $('#date').val();
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.area_group = $('#area_group').val();
                    d.area = $('#area').val();

                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
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
                // {
                //     data: 'donateItems',
                //     name: 'donateItems',
                //     orderable: false,
                //     searchable: false
                // },
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
            lengthMenu: [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]], 
            pageLength: 10,
            responsive: true,
            language: languages[language], // Apply language dynamically
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            },
            createdRow: function (row, data, dataIndex) {

            // Get the parent ID or group ID from the data
            const parentId = data.parent_donor_group_id;

            // Generate a unique color for this parent
            const color = getColorForParent(parentId);

            if (data.is_child === 'Parent') {
                // For child rows, apply the color to all columns
                $(row).addClass('child-row');
                $(row).find('td').attr('style', 'background-color: ' + color + ' !important');
            } else if (data.is_child === 'Child') {
                // For parent rows, apply the color only to the first column
                $(row).find('td:first').attr('style', 'background-color: ' + color + ' !important');
            }
            }
        });

        // Initialize DataTable
        monthlyFormstable = $('#monthly-forms-table').DataTable({
            ajax: {
                url: "{{ route('collecting-lines.monthly-forms') }}",
                data: function(d) {
                    // d.date = $('#date').val();
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.area_group = $('#area_group').val();
                    d.area = $('#area').val();

                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
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
                    searchable: true,
                    render: function(data, type, row) {
                        console.log(data);
                        if (!data) return '<div>N/A</div>';
                        return data
                            .split(', ')
                            .map(phone => `<div>${phone}</div>`)
                            .join('');
                    }

                },
                {
                    data: 'notes',
                    nmae: 'notes'
                },
                {
                    data: 'collecting_donation_way',
                    name: 'collecting_donation_way'
                },
                {
                    data: 'monthly_donation_day',
                    name: 'monthly_donation_day'
                },

                {
                    data: 'items', // Add the 'items' column
                    name: 'items',
                    orderable: false,
                    searchable: false
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Monthly Forms Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            lengthMenu: [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]], 
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            },
            createdRow: function (row, data, dataIndex) {

                // Get the parent ID or group ID from the data
                const parentId = data.parent_donor_group_id;

                // Generate a unique color for this parent
                const color = getColorForParent(parentId);

                if (data.is_child === 'Parent') {
                    // For child rows, apply the color to all columns
                    $(row).addClass('child-row');
                    $(row).find('td').attr('style', 'background-color: ' + color + ' !important');
                } else if (data.is_child === 'Child') {
                    // For parent rows, apply the color only to the first column
                    $(row).find('td:first').attr('style', 'background-color: ' + color + ' !important');
                }
            }
        });


        function getColorForParent(parentId) {
            // Use a hash function to generate a unique color
            const colors = [
                '#f9f9f9', '#e6f7ff', '#fff7e6', '#e6ffe6', '#ffe6e6', 
                '#e6e6ff', '#f0e6ff', '#ffe6f0', '#e6fff0', '#fff0e6'
            ];
            const index = parentId % colors.length; // Ensure the index is within the array bounds
            return colors[index];
        }


        $('#date-filter').on('change', function () {
        if ($(this).val() === 'range') {
            $('#custom-range, #end-date-container').show();
        } else {
            $('#custom-range, #end-date-container').hide();
            $('#start-date, #end-date').val('');
        }
        collectingLinesTable.ajax.reload();
        donationsTable.ajax.reload();
        monthlyFormstable.ajax.reload();
    });

    $('#start-date, #end-date').on('change', function () {
        collectingLinesTable.ajax.reload();
        donationsTable.ajax.reload();
        monthlyFormstable.ajax.reload();
    });

    $('#clear-filters').on('click', function() {
        $('#date-filter').val('all').trigger('change');
        $('#start-date, #end-date').val('');
        collectingLinesTable.ajax.reload();
        donationsTable.ajax.reload();
        monthlyFormstable.ajax.reload();
    });


        // Apply Filters
        $('#filter-btn').on('click', function() {
            collectingLinesTable.ajax.reload();
            donationsTable.ajax.reload();
            monthlyFormstable.ajax.reload();
        });

        // Add Collecting Line
        $('#addCollectingLineForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('collecting-lines.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addCollectingLineModal').modal('hide');
                    collectingLinesTable.ajax.reload();
                }
            });
        });

        // Edit Collecting Line
        $('#collecting-lines-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_representative_id').val($(this).data('representative-id'));
            $('#edit_driver_id').val($(this).data('driver-id'));
            $('#edit_employee_id').val($(this).data('employee-id'));
            $('#edit_area_group_id').val($(this).data('area-group-id'));
            $('#edit_collecting_date').val($(this).data('collecting-date'));
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
                    $('#editCollectingLineModal').modal('hide');
                    collectingLinesTable.ajax.reload();
                }
            });
        });

        // Delete Collecting Line
        $('#collecting-lines-table').on('click', '.delete-btn', function() {
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
                    collectingLinesTable.ajax.reload();
                }
            });
        });

        // Initialize DataTable for selecting collecting lines
        var selectCollectingLinesTable = $('#select-collecting-lines-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('collecting-lines.data') }}",
                data: function(d) {
                    d.date = $('#date').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
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
                    searchable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-primary select-collecting-line-btn" data-id="${row.id}">Select</button>`;
                    }
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

        // Handle the selection of a collecting line
        $('#select-collecting-lines-table').on('click', '.select-collecting-line-btn', function() {
            var collectingLineId = $(this).data('id');
            var donationId = $(this).closest('tr').data('donation-id'); // Use data attribute for donation ID

            if (collectingLineId && donationId) {
                $.ajax({
                    url: "/donations/" + donationId + "/assign",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        collecting_line_id: collectingLineId
                    },
                    success: function(response) {
                        $('#selectCollectingLineModal').modal('hide');
                        donationsTable.ajax.reload();
                        alert(response.success);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'An error occurred.');
                    }
                });
            }
        });

        // Open the View Donations Modal when the "View Donations" button is clicked
        $('#collecting-lines-table').on('click', '.view-donations-btn', function() {
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
                            d.date = $('#date').val();
                            d.area_group = $('#area_group').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
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



    });

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Function to format date
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Function to add monthly form donation
    function addMonthlyFormDonation(monthlyFormId) {
        $('#addMonthlyFormDonationModal').modal('show');

        $.get(`{{ url('monthly-forms') }}/${monthlyFormId}/edit`)
            .done(function(data) {
                // Get today's date in YYYY-MM-DD format
                const today = new Date().toISOString().split('T')[0];
                $('#add_monthly_form_id').val(monthlyFormId);
                // Populate basic fields
                $('#add_donor_id').val(data.donor_id).trigger('change');
                $('#add_date').val($('#date').val());
                $('#add_donation_status').val('not_collected').trigger('change');
                $('#add_donation_type').val(data.donation_type).trigger('change');
                $('#add_reporting_way').val('call').trigger('change');
                $('#add_collecting_date').val(formatDate(data.collecting_donation?.collecting_date));
                $('#add_in_kind_receipt_number').val(data.collecting_donation?.in_kind_receipt_number || '');
                $('#add_employee_id').val(data.collecting_donation?.employee_id).trigger('change');
                $('#add_notes').val(data.donor.notes || '');
                $('#add_collecting_time').val(data.collecting_time || '');
                $('#add_collecting_way').val(data.collecting_way || '');

                const financialContainer = $('#add-financial-donation-rows-container');
                const inKindContainer = $('#add-in-kind-donation-rows-container');
                financialContainer.empty();
                inKindContainer.empty();

                const financialSection = document.getElementById('add-financial-donations-section');
                const inKindSection = document.getElementById('add-in-kind-donations-section');

                if (data.donation_type === 'financial') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.add('d-none');

                    // Populate financial donations
                    data.items
                        .filter(donation => donation.donation_type === 'financial')
                        .forEach((donation, index) => {
                            financialContainer.append(renderFinancialRowInAddMonthlyForm(donation, index, donationCategories));
                        });

                    $('#add-financial-row-edit').show();
                }

                if (data.donation_type === 'inKind') {
                    financialSection.classList.add('d-none');
                    inKindSection.classList.remove('d-none');

                    // Populate in-kind donations
                    data.items
                        .filter(donation => donation.donation_type === 'inKind')
                        .forEach((donation, index) => {
                            inKindContainer.append(renderInKindRowInAddMonthlyForm(donation, index));
                        });
                }

                if (data.donation_type === 'both') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.remove('d-none');

                    // Populate financial donations
                    data.items
                        .filter(donation => donation.donation_type === 'financial')
                        .forEach((donation, index) => {
                            financialContainer.append(renderFinancialRowInAddMonthlyForm(donation, index, donationCategories));
                        });

                    // Populate in-kind donations
                    data.items
                        .filter(donation => donation.donation_type === 'inKind')
                        .forEach((donation, index) => {
                            inKindContainer.append(renderInKindRowInAddMonthlyForm(donation, index));
                        });
                }
            })
            .fail(function() {
                alert('{{ __("Failed to load donation details. Please try again.") }}');
            });
    }


    
    // Function to render a financial row
    function renderFinancialRowInAddMonthlyForm(donation, index, categories) {
            const categoryOptions = categories.map(category =>
                `<option value="${category.id}" ${Number(category.id) === Number(donation.donation_category_id || 0) ? 'selected' : ''}>
                ${category.name}
            </option>`
            ).join('');

            return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][financial_donation_type]" value="financial">
            <input type="hidden" name="donates[${index}][financial_monthly_donation_id]" value="${donation.id}">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Category')}}</label>
                    <select class="form-control donation-category" name="donates[${index}][financial_donation_categories_id]">
                        ${categoryOptions}
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">{{__('Amount')}}</label>
                    <input type="number" class="form-control amount" name="donates[${index}][financial_amount]" value="${donation.amount}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="receipt_number" class="form-label">{{__('Financial Receipt Number')}}</label>
                                        <input type="text" class="form-control" name="donates[${index}][financial_receipt_number]"
                                        value="${donation.financial_receipt_number || ''}">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][financial_donation_item_type]">
                        <option value="monthly">{{__('Monthly')}}</option>
                        <option value="normal">{{__('Normal')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
            </div>
        </div>`;
        }



    // Function to render an in-kind row
    function renderInKindRowInAddMonthlyForm(donation, index) {
            return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][in_kind_donation_type]" value="inKind">
            <input type="hidden" name="donates[${index}][in_kind_monthly_donation_id]" value="${donation.id || ''}">
            <div class="col-md-3">
                <label class="form-label">{{__('Item Name')}}</label>
                <input type="text" class="form-control" name="donates[${index}][in_kind_item_name]" value="${donation.item_name || ''}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{__('Quantity')}}</label>
                <input type="number" class="form-control" name="donates[${index}][in_kind_quantity]" value="${donation.amount || ''}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-3">
            <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][in_kind_donation_item_type]">
                        <option value="monthly">{{__('Monthly')}}</option>
                        <option value="normal">{{__('Normal')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
            </div>
        </div>`;
        }


    // Function to render a financial row
    function renderFinancialRow(donation, index, categories) {
        const categoryOptions = categories.map(category =>
            `<option value="${category.id}" ${Number(category.id) === Number(donation.donation_category_id || 0) ? 'selected' : ''}>
            ${category.name}
        </option>`
        ).join('');

        return `
    <div class="row donation-row">
        <input type="hidden" name="donates[${index}][financial_donation_type]" value="financial">
        <input type="hidden" name="donates[${index}][financial_monthly_donation_id]" value="${donation.id}">
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">{{__('Donation Category')}}</label>
                <select class="form-control donation-category" name="donates[${index}][financial_donation_categories_id]">
                    ${categoryOptions}
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">{{__('Amount')}}</label>
                <input type="number" class="form-control amount" name="donates[${index}][financial_amount]" value="${donation.amount}">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
        </div>
    </div>`;
    }

    // Function to render an in-kind row
    function renderInKindRow(donation, index) {
        return `
    <div class="row donation-row">
        <input type="hidden" name="donates[${index}][inKind_donation_type]" value="inKind">
        <input type="hidden" name="donates[${index}][inKind_monthly_donation_id]" value="${donation.id || ''}">
        <div class="col-md-4">
            <label class="form-label">{{__('Item Name')}}</label>
            <input type="text" class="form-control" name="donates[${index}][in_kind_item_name]" value="${donation.item_name || ''}">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{__('Quantity')}}</label>
            <input type="number" class="form-control" name="donates[${index}][in_kind_quantity]" value="${donation.amount || ''}">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
        </div>
    </div>`;
    }

    // Function to toggle donation status visibility
    function toggleEditDonationStatus() {
        const donationStatus = document.getElementById('add_donation_status').value;
        const collectingSection = document.getElementById('add-collecting-section');

        if (donationStatus === 'collected') {
            collectingSection.classList.remove('d-none');
        } else if (donationStatus === 'not_collected') {
            collectingSection.classList.add('d-none');
        }
    }

    // Function to toggle donation type visibility
    function toggleEditDonationType() {
        const donationType = document.getElementById('add_donation_type').value;
        const financialSection = document.getElementById('add-financial-donations-section');
        const inKindSection = document.getElementById('add-in-kind-donations-section');

        if (donationType === 'financial') {
            financialSection.classList.remove('d-none');
            inKindSection.classList.add('d-none');
        } else if (donationType === 'inKind') {
            financialSection.classList.add('d-none');
            inKindSection.classList.remove('d-none');
        } else {
            financialSection.classList.remove('d-none');
            inKindSection.classList.remove('d-none');
        }
    }

    // Add event listeners for "Add Row" buttons
    $(document).on('click', '#add-financial-row-edit', function() {
        const financialContainer = $('#add-financial-donation-rows-container');
        const index = financialContainer.children().length;
        financialContainer.append(renderFinancialRowInAddMonthlyForm({}, index, donationCategories));
    });

    $(document).on('click', '#add-in-kind-row-edit', function() {
        const inKindContainer = $('#add-in-kind-donation-rows-container');
        const index = inKindContainer.children().length;
        inKindContainer.append(renderInKindRowInAddMonthlyForm({}, index));
    });

    // Handle form submission
    $('#addMonthlyFormDonationForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const monthlyFormId = $('#add_monthly_form_id').val();
        $.ajax({
            url: `{{ url('monthly-forms') }}/${monthlyFormId}/donations`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addMonthlyFormDonationModal').modal('hide');
                collectingLinesTable.ajax.reload();
                donationsTable.ajax.reload();
                monthlyFormstable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                  // Validation error
                  let response = xhr.responseJSON;
                  let errors = response.errors || {};
                  let errorDetails = '';

                  // Loop through each field and its errors
                  for (let field in errors) {
                      if (errors.hasOwnProperty(field)) {
                          let fieldErrors = errors[field].join(', ');
                          errorDetails += `<p>${fieldErrors}</p>`;
                      }
                  }

                  Swal.fire({
                      icon: 'error',
                      title: '{{ __('validation.Validation Error ') }}', // Ensure this is rendered as a string by Blade
                      html: `<div style="direction: rtl; text-align: center;">${errorDetails}</div>`,
                      customClass: {
                          popup: 'text-start',
                      }
                  });

              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: xhr.responseJSON.message || 'Something went wrong!'
                  });
              }
            }
        });
    });

    // Handle "Select" button click
    $(document).on('click', '.assign-btn', function() {
        var donationId = $(this).data('id'); // Get the donation ID
        $('#donation_id').val(donationId); // Set the donation ID in the form

        // Fetch collecting lines based on the selected date filter
        var selectedDate = $('#date').val(); // Assuming you have a date filter input with ID "date"

        $.ajax({
            url: "{{ route('collecting-lines.get-data-by-date') }}", // Route to fetch collecting lines
            type: "GET",
            data: {
                date: selectedDate
            },
            success: function(response) {
                // Clear the select options
                $('#collecting_line_id').empty();

                // Populate the select options
                if (response.data.length > 0) {
                    $.each(response.data, function(index, collectingLine) {
                        $('#collecting_line_id').append(
                            `<option value="${collectingLine.id}">(${collectingLine.number}) - ${collectingLine.area_group.name} - (${collectingLine.collecting_date})</option>`
                        );
                    });
                } else {
                    $('#collecting_line_id').append(`<option value="">{{__('No Collecting Lines Found')}}</option>`);
                }

                // Show the modal
                $('#assignDonationModal').modal('show');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    // Handle form submission
    $('#assignDonationSubmit').on('click', function() {
        var formData = $('#assignDonationForm').serialize();

        $.ajax({
            url: "{{ route('collecting-lines.assign-donation') }}", // Route to assign donation
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    });

                    // Close the modal
                    $('#assignDonationModal').modal('hide');

                    // Reload the DataTable (if needed)
                    $('#donations-table').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    for (var key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            var input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(errors[key][0]);
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Something went wrong!',
                    });
                }
            }
        });
    });







    $(function() {
      

      let financialRowIndex = 0;
      let inKindRowIndex = 0;

        $(document).on('click', '.add-row-btn', function() {
          const container = $($(this).data('target'));
          if (container.attr('id') === 'financial-donation-rows-container' || container.attr('id') === 'edit-financial-donation-rows-container') {
              // Generate a unique index for financial donations
              let newIndex = 0;
              while (existingFinancialIndices.has(newIndex)) {
                  newIndex++;
              }
              existingFinancialIndices.add(newIndex); // Track the new index

              container.append(`
          <div class="row donation-row">
              <input type="hidden" name="donates[${newIndex}][financial_donation_type]" value="financial">
              <div class="col-md-3">
                  <div class="mb-3">
                      <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                      <select class="form-control donation-category" name="donates[${newIndex}][financial_donation_categories_id]">
                          <option value="">{{__('Select Category')}}</option>
                          @foreach($donationCategories as $category)
                              <option value="{{ $category->id }}">{{ $category->name }}</option>
                          @endforeach
                      </select>
                      <div class="invalid-feedback"></div>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="mb-3">
                      <label for="amount" class="form-label">{{__('Amount')}}</label>
                      <input type="number" class="form-control amount" name="donates[${newIndex}][financial_amount]">
                      <div class="invalid-feedback"></div>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="mb-3">
                      <label for="amount" class="form-label">{{__('Financial Receipt Number')}}</label>
                      <input type="text" class="form-control amount" name="donates[${newIndex}][financial_receipt_number]">
                      <div class="invalid-feedback"></div>
                  </div>
              </div>
              <div class="col-md-3 d-flex align-items-center">
                  <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
              </div>
          </div>
      `);
          } else if (container.attr('id') === 'in-kind-donation-rows-container' || container.attr('id') === 'edit-in-kind-donation-rows-container') {
              // Generate a unique index for in-kind donations
              let newIndex = 0;
              while (existingInKindIndices.has(newIndex)) {
                  newIndex++;
              }
              existingInKindIndices.add(newIndex); // Track the new index

              container.append(`
          <div class="row donation-row">
              <input type="hidden" name="donates[${newIndex}][inKind_donation_type]" value="inKind">
              <div class="col-md-4">
                  <div class="mb-3">
                      <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                      <input type="text" class="form-control" name="donates[${newIndex}][in_kind_item_name]">
                      <div class="invalid-feedback"></div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="mb-3">
                      <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                      <input type="number" class="form-control" name="donates[${newIndex}][in_kind_quantity]">
                      <div class="invalid-feedback"></div>
                  </div>
              </div>
              <div class="col-md-4 d-flex align-items-center">
                  <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
              </div>
          </div>
      `);
          }
      });


      $(document).on('click', '.remove-row-btn', function() {
          const row = $(this).closest('.donation-row');
          const input = row.find('input[name*="donates"]');
          const name = input.attr('name');
          const index = name.match(/\[(\d+)\]/)[1]; // Extract the index from the input name

          if (name.includes('financial_donation_type')) {
              existingFinancialIndices.delete(parseInt(index)); // Remove the index from the set
          } else if (name.includes('inKind_donation_type')) {
              existingInKindIndices.delete(parseInt(index)); // Remove the index from the set
          }

          row.remove(); // Remove the row from the DOM
      });

      // Handle Remove Row buttons
      document.addEventListener('click', function(e) {
          if (e.target.classList.contains('remove-row-btn')) {
              e.target.closest('.donation-row').remove();
          }
      });



      $('.modal').on('hidden.bs.modal', function() {
          var form = $(this).find('form');
          form.find('.is-invalid').removeClass('is-invalid');
          form.find('.invalid-feedback').text('');
      });



  });



  $(document).ready(function() {


      $('#edit_donor_id').select2({
          dropdownParent: $('#editDonationModal'),
          placeholder: "{{__('Search By Name or Phone')}}",
          ajax: {
              url: '{{ route("donors.search") }}',
              dataType: 'json',
              delay: 250,
              data: function(params) {
                  return {
                      query: params.term // Search query
                  };
              },
              processResults: function(data) {
                  return {
                      results: data.results.map(function(donor) {
                          return {
                              id: donor.id,
                              text: `${donor.text}`, // Display the name and the exact phone that matched the search term
                          };
                      })
                  };
              },
              // cache: true
          },
          templateResult: function(donor) {
              if (donor.loading) return donor.text;

              return $('<span>' + donor.text + '</span>'); // Display donor name and matched phone in the dropdown
          },
          templateSelection: function(donor) {
              return donor.text; // When selected, show name and matched phone
          }
      });


      $('#add-in-kind-row-edit').on('click', function() {
          const index = $('#edit-in-kind-donation-rows-container .donation-row').length;
          $('#edit-in-kind-donation-rows-container').append(renderInKindRow({}, index));
      });


  });


  function toggleEditDonationType() {
      const donationType = document.getElementById('edit_donation_type').value;
      const editFinancialContainer = document.getElementById('edit-financial-donations-section');
      const editInKindContainer = document.getElementById('edit-in-kind-donations-section');
      const financialReceiptConatiner = document.getElementById('edit-financial-receipt-container');
      const inKindReceiptConatiner = document.getElementById('edit-in-kind-receipt-container');

      if (donationType === 'financial') {
          editFinancialContainer.classList.remove('d-none');
          editInKindContainer.classList.add('d-none');
          // financialReceiptConatiner.classList.remove('d-none');
          inKindReceiptConatiner.classList.add('d-none');
      } else if (donationType === 'inKind') {
          editFinancialContainer.classList.add('d-none');
          editInKindContainer.classList.remove('d-none');
          // financialReceiptConatiner.classList.add('d-none');
          inKindReceiptConatiner.classList.remove('d-none');
      } else {
          editFinancialContainer.classList.remove('d-none');
          editInKindContainer.classList.remove('d-none');
          // financialReceiptConatiner.classList.remove('d-none');
          inKindReceiptConatiner.classList.remove('d-none');
      }
  }


  function toggleEditDonationStatus() {
      const donationStatus = document.getElementById('edit_donation_status').value;
      const CollectingSection = document.getElementById('edit-collecting-section');

      if (donationStatus === 'collected') {
          CollectingSection.classList.remove('d-none');
      } else if (donationStatus === 'not_collected') {
          CollectingSection.classList.add('d-none');
      }
  }



  function toggleAddDonationType() {
      const donationType = document.getElementById('add_donation_type').value;
      const editFinancialContainer = document.getElementById('add-financial-donations-section');
      const editInKindContainer = document.getElementById('add-in-kind-donations-section');
      const financialReceiptConatiner = document.getElementById('add-financial-receipt-container');
      const inKindReceiptConatiner = document.getElementById('add-in-kind-receipt-container');

      if (donationType === 'financial') {
          editFinancialContainer.classList.remove('d-none');
          editInKindContainer.classList.add('d-none');
          // financialReceiptConatiner.classList.remove('d-none');
          inKindReceiptConatiner.classList.add('d-none');
      } else if (donationType === 'inKind') {
          editFinancialContainer.classList.add('d-none');
          editInKindContainer.classList.remove('d-none');
          // financialReceiptConatiner.classList.add('d-none');
          inKindReceiptConatiner.classList.remove('d-none');
      } else {
          editFinancialContainer.classList.remove('d-none');
          editInKindContainer.classList.remove('d-none');
          // financialReceiptConatiner.classList.remove('d-none');
          inKindReceiptConatiner.classList.remove('d-none');
      }
  }


  function toggleAddDonationStatus() {
      const donationStatus = document.getElementById('add_donation_status').value;
      const CollectingSection = document.getElementById('add-collecting-section');

      if (donationStatus === 'collected') {
          CollectingSection.classList.remove('d-none');
      } else if (donationStatus === 'not_collected') {
          CollectingSection.classList.add('d-none');
      }
  }




  function donationDetails(id) {
      $('#detailsDonationModal').modal('show');

      $.get(`{{ url('donations') }}/${id}/details`)
          .done(function(data) {
              // Construct the content to be displayed in the modal
              let modalContent = `
          <h5>{{__('Donor Information')}}</h5>
          <div class="row">
              <div class="col-md-3">
                          <p><strong>{{__('Name')}}:</strong> ${data.donor.name}</p>
              </div>
               <div class="col-md-3">
                <p><strong>{{__('Donor Type')}}:</strong> 
                  ${data.donor.donor_type === 'normal' ? '{{__("Normal")}}' : '{{__("Monthly")}}'}
                  </p>
              </div>
              <div class="col-md-6">
                          <p><strong>{{__('Address')}}:</strong> ${data.donor.address}</p>
              </div>
             

          </div>
        

          <h5>{{__('Donation Information')}}</h5>
           <div class="row">
              <div class="col-md-3">
                  <p><strong>{{__('Donation Type')}}:</strong> ${data.donation_type}</p>
              </div>
              <div class="col-md-3">
                  <p><strong>{{__('Donation Date')}}:</strong> ${data.date}</p>
              </div>
              <div class="col-md-3">
                  <p><strong>{{__('Status')}}:</strong> 
              
                  ${data.status === 'collected' ? '{{__("Collected")}}' : '{{__("Not Collected")}}'}</p>
              </div>
              </div>
               <div class="row">
                <div class="col-md-3">
                  <p><strong>{{__('Reporting Date')}}:</strong> ${data.created_at}</p>
               </div>
               <div class="col-md-3">
                  <p><strong>{{__('Reporting Way')}}:</strong> ${data.reporting_way}</p>
               </div>
                <div class="col-md-3">
                  <p><strong>{{__('Created By')}}:</strong> ${data.created_by?.name}</p>
               </div>
                <div class="col-md-3">
                  <p><strong>{{__('Department')}}:</strong> ${data.created_by?.department?.name ?? 'N/A'}</p>
               </div>
               </div>
                <div class="row">
                <div class="col-md-6">
                  <p><strong>{{__('Collecting Time')}}:</strong> ${data.collecting_time}</p>
                </div>
                  <div class="col-md-6">
                  <p><strong>{{__('Notes')}}:</strong> ${data.notes}</p>
                </div>
                </div>
     `;

              // Financial Donations Table
              if (data.donation_type === 'financial' || data.donation_type === 'both') {
                  modalContent += `
              <h5>{{__('Financial Donations')}}</h5>
              <table class="table table-striped">
                  <thead>
                      <tr>
                          <th>{{__('Donation Category')}}</th>
                          <th>{{__('Amount')}}</th>
                          <th>{{__('Financial Receipt Number')}}</th>
                      </tr>
                  </thead>
                  <tbody>`;

                  data.donate_items
                      .filter(item => item.donation_type === 'financial')
                      .forEach(item => {
                          modalContent += `
                      <tr>
                          <td>${item.donation_category.name}</td>
                          <td>${item.amount}</td>
                          <td>${item.financial_receipt_number ?? 'N/A'}</td>
                      </tr>`;
                      });

                  modalContent += `
                  </tbody>
              </table>`;
              }

              // In-Kind Donations Table
              if (data.donation_type === 'inKind' || data.donation_type === 'both') {
                  modalContent += `
              <h5>{{__('In-Kind Donations')}}</h5>
              <table class="table table-striped">
                  <thead>
                      <tr>
                          <th>{{__('Item Name')}}</th>
                          <th>{{__('Amount')}}</th>
                      </tr>
                  </thead>
                  <tbody>`;

                  data.donate_items
                      .filter(item => item.donation_type === 'inKind')
                      .forEach(item => {
                          modalContent += `
                      <tr>
                          <td>${item.item_name}</td>
                          <td>${item.amount}</td>
                      </tr>`;
                      });

                  modalContent += `
                  </tbody>
              </table>`;
              }

              // Collecting Donation Info
              modalContent += `
          <h5>{{__('Collecting Donation Information')}}</h5>
          <p><strong>{{__('Collecting Date')}}:</strong> ${data.collecting_donation?.collecting_date ? formatDate(data.collecting_donation?.collecting_date) : 'N/A'}</p>
          <p><strong>{{__('In Kind Receipt Number')}}:</strong> ${data.collecting_donation?.in_kind_receipt_number ?? 'N/A'}</p>`;

              // Add the constructed content to the modal body
              $('#detailsDonationModal .modal-body').html(modalContent);

          })
          .fail(function() {
              alert('{{ __("Failed to load donation details. Please try again.") }}');
          });
  }



  // Helper function to format date in YYYY-MM-DD
  function formatDate(dateString) {
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
  }

  let existingFinancialIndices = new Set();
  let existingInKindIndices = new Set();

  function editDonation(id) {
      var form = $('#editDonationForm');
      form.trigger('reset');
      form.find('.is-invalid').removeClass('is-invalid');
      form.find('.invalid-feedback').text('');
      form.attr('action', `{{ route('donations.update', '') }}/${id}`);
      $('#editDonationModal').modal('show');

      $.get(`{{ url('donations') }}/${id}/edit`)
          .done(function(data) {
              // Reset existing indices
              existingFinancialIndices = new Set();
              existingInKindIndices = new Set();

              // Populate basic fields
              $('#edit_donor_id').val(data.donor_id).trigger('change');
              $('#edit_date').val(data.date);
              $('#edit_donation_status').val(data.status).trigger('change');
              $('#edit_donation_type').val(data.donation_type).trigger('change');
              $('#edit_reporting_way').val(data.reporting_way).trigger('change');
              $('#edit_collecting_date').val(formatDate(data.collecting_donation?.collecting_date));
              $('#edit_in_kind_receipt_number').val(data.collecting_donation?.in_kind_receipt_number);
              $('#edit_employee_id').val(data.collecting_donation?.employee_id).trigger('change');
              $('#edit_notes').val(data.notes);
              $('#edit_collecting_time').val(data.collecting_time);
              $('#edit_collecting_way').val(data.collecting_way);

              // Populate financial donations
              const financialContainer = $('#edit-financial-donation-rows-container');
              financialContainer.empty();
              data.donate_items
                  .filter(item => item.donation_type === 'financial')
                  .forEach((donation, index) => {
                      existingFinancialIndices.add(index); // Track existing indices
                      financialContainer.append(renderFinancialRow(donation, index, donationCategories));
                  });

              // Populate in-kind donations
              const inKindContainer = $('#edit-in-kind-donation-rows-container');
              inKindContainer.empty();
              data.donate_items
                  .filter(item => item.donation_type === 'inKind')
                  .forEach((donation, index) => {
                      existingInKindIndices.add(index); // Track existing indices
                      inKindContainer.append(renderInKindRow(donation, index));
                  });

              // Toggle sections based on donation type
              toggleEditDonationType();
              toggleEditDonationStatus();
          })
          .fail(function() {
              alert('{{ __("Failed to load donation details. Please try again.") }}');
          });
  }


  function renderFinancialRow(donation, index, categories) {
      console.log(donation, index, categories);
      const categoryOptions = categories.map(category =>
          `<option value="${category.id}" ${category.id == donation.donation_category_id ? 'selected' : ''}>${category.name}</option>`
      ).join('');

      return `
      <div class="row donation-row">
          <input type="hidden" name="donates[${index}][financial_donation_type]" value="financial">
          <input type="hidden" name="donates[${index}][financial_donation_id]" value="${donation.id || ''}">
          <div class="col-md-3">
              <label class="form-label">{{__('Donation Category')}}</label>
              <select class="form-control" name="donates[${index}][financial_donation_categories_id]">
                  ${categoryOptions}
              </select>
              <div class="invalid-feedback"></div>
          </div>
          <div class="col-md-2">
              <label class="form-label">{{__('Amount')}}</label>
              <input type="number" class="form-control" name="donates[${index}][financial_amount]" value="${donation.amount || ''}">
              <div class="invalid-feedback"></div>
          </div>
           <div class="col-md-3">
              <label class="form-label">{{__('Financial Receipt Number')}}</label>
              <input type="text" class="form-control" name="donates[${index}][financial_receipt_number]" value="${donation.financial_receipt_number || ''}">
              <div class="invalid-feedback"></div>
          </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][financial_donation_item_type]">
                        <option value="normal" ${donation.donation_item_type === 'normal' ? 'selected' : ''}>{{__('Normal')}}</option>
                        <option value="monthly" ${donation.donation_item_type === 'monthly' ? 'selected' : ''}>{{__('Monthly')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
          <div class="col-md-1 d-flex align-items-center">
              <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
          </div>
      </div>`;
  }

  function renderInKindRow(donation, index) {
      return `
      <div class="row donation-row">
          <input type="hidden" name="donates[${index}][inKind_donation_type]" value="inKind">
          <input type="hidden" name="donates[${index}][inKind_donation_id]" value="${donation.id || ''}">
          <div class="col-md-3">
              <label class="form-label">{{__('Item Name')}}</label>
              <input type="text" class="form-control" name="donates[${index}][in_kind_item_name]" value="${donation.item_name || ''}">
              <div class="invalid-feedback"></div>
          </div>
          <div class="col-md-3">
              <label class="form-label">{{__('Quantity')}}</label>
              <input type="number" class="form-control" name="donates[${index}][in_kind_quantity]" value="${donation.amount || ''}">
              <div class="invalid-feedback"></div>
          </div>
          <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][in_kind_donation_item_type]">
                        <option value="normal" ${donation.donation_item_type === 'normal' ? 'selected' : ''}>{{__('Normal')}}</option>
                        <option value="monthly" ${donation.donation_item_type === 'monthly' ? 'selected' : ''}>{{__('Monthly')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
          <div class="col-md-3 d-flex align-items-center">
              <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
          </div>
      </div>`;
  }

  function toggleAddRowButton() {
      if ($('#edit-financial-donation-rows-container .donation-row').length === 0) {
          $('#add-financial-row-edit').show(); // Show button if no rows
      } else {
          $('#add-financial-row-edit').hide(); // Hide button if there are rows
      }
  }

  // Remove Row Edit with SweetAlert Confirmation
  $(document).on('click', '.remove-row-btn-edit', function() {
      const row = $(this).closest('.donation-row');
      const donationId = row.find('input[name*="_donation_id"]').val(); // Extract donation ID

      console.log(donationId);
      if (donationId === 'undefined' || donationId === '') {
        $(this).closest('.row').remove();
        return;
      }
    //   if (!donationId) {
    //       Swal.fire({
    //           icon: 'error',
    //           title: 'Oops...',
    //           text: 'Donation ID not found.'
    //       });
    //       return;
    //   }

    if(donationId !== 'undefined'){
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: `{{ url('donations/delete-donatation-item') }}/${donationId}`, // Endpoint to handle deletion
                  type: 'DELETE',
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                  },
                  success: function(response) {
                      Swal.fire(
                          'Deleted!',
                          response.message || 'Donation removed successfully.',
                          'success'
                      );
                      row.remove(); // Remove the row from the DOM
                      toggleAddRowButton();
                      donationsTable.ajax.reload();

                  },
                  error: function(error) {
                      console.error("Error deleting donation:", error);
                      Swal.fire(
                          'Error!',
                          'Failed to delete the donation. Please try again.',
                          'error'
                      );
                  }
              });
          }
      });
    }
  });

  // Edit Donation Form Submit
  $('#editDonationForm').on('submit', function(e) {
      e.preventDefault();
      var form = $(this);
      var url = form.attr('action');

      $.ajax({
          url: url,
          type: 'POST',
          data: form.serialize(),
          headers: {
              'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
              if (response.success) {
                  $('#editDonationModal').modal('hide');
                  form[0].reset();
                  donationsTable.ajax.reload();
                  Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: response.message
                  });
              }
          },
          error: function(xhr) {
              if (xhr.status === 422) {
                  // Validation error
                  let response = xhr.responseJSON;
                  let errors = response.errors || {};
                  let errorDetails = '';

                  // Loop through each field and its errors
                  for (let field in errors) {
                      if (errors.hasOwnProperty(field)) {
                          let fieldErrors = errors[field].join(', ');
                          errorDetails += `<p>${fieldErrors}</p>`;
                      }
                  }

                  Swal.fire({
                      icon: 'error',
                      title: '{{ __('validation.Validation Error ') }}', // Ensure this is rendered as a string by Blade
                      html: `<div style="direction: rtl; text-align: center;">${errorDetails}</div>`,
                      customClass: {
                          popup: 'text-start',
                      }
                  });

              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: xhr.responseJSON.message || 'Something went wrong!'
                  });
              }
          }
      });
  });


  function addActivity(donorId) {
    $('#addActivityModal').modal('show');
    $('#donor_id').val(donorId);
    

}

$('#addActivityForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addActivityModal').modal('hide');
                    form[0].reset();
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        var input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                }
            }
        });
    });


    document.addEventListener('keydown', function(event) {
    // Check if Ctrl (or Cmd on Mac) is pressed
        if (event.ctrlKey || event.metaKey) {
            // Prevent the default behavior (if needed)
            event.preventDefault();

        }
        if (event.key === 'F2') {
            // Check if the "Add Monthly Form" modal is open
            if ($('#addMonthlyFormDonationModal').is(':visible')) {
                $('#addMonthlyFormDonationForm').submit(); // Submit the "Add" form
            }

        }
});


    
</script>
@endpush