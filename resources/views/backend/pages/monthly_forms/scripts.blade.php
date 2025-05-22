@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let monthlyFormsTable;
    $(function() {
        // Initialize DataTable
        var monthlyFormsTable = $('#monthly-forms-table').DataTable({
            ajax: {
                url: "{{ route('monthly-forms.data') }}",
                data: function(d) {
                    d.status = 'ongoing'; // Add the status parameter
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.department = $('#department-filter').val();
                    d.follow_up_department = $('#follow-up-department-filter').val();
                    d.employee = $('#employee-filter').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',

                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,

                },
                {
                    data: 'area',
                    name: 'area',
                    orderable: false,

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
                    },
                    orderable: false,


                },
                {
                    data: 'monthly_donation_day',
                    name: 'monthly_donation_day',
                    orderable: false,

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
            initComplete: function() {
                // Apply column-specific search
                this.api().columns().every(function() {
                    var column = this;
                    $('input, select', column.header()).on('change keyup', function() {
                        column.search($(this).val()).draw();
                    });
                });
            },
            order: [
                [0, 'desc']
            ],
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Monthly Forms Data',
                    exportOptions: {
                        columns: ':visible',
                        header: true,
                        format: {
                            body: function(data, row, column, node) {
                                if (column === 3) { // Ensure this is the correct index for 'phones'
                                    let extractedPhones = $('<div>').html(data).children('div').map(function() {
                                        return $(this).text().trim(); // Extract text from each <div>
                                    }).get().join(' --- '); // Change ' --- ' to '/' if needed

                                    return extractedPhones || "N/A"; // Ensure non-empty output
                                }

                                return $('<div>').html(data).text().trim(); // Remove HTML from other columns
                            },
                            header: function(data, columnIdx) {
                                if ($('#donors-table thead tr:first-child th').eq(columnIdx).length) {
                                    return $('#donors-table thead tr:first-child th').eq(columnIdx).text();
                                }
                                return '';
                            }
                        }
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var columnIndex = 6;

                var totalFinancial = api
                    .rows({
                        search: 'applied'
                    })
                    .data()
                    .pluck('financial_amount')
                    .reduce((acc, val) => acc + parseFloat(val || 0), 0);

                var footerCell = $(api.column(columnIndex).footer());
                if (footerCell.length) {
                    footerCell.html(`<strong>${totalFinancial.toFixed(2)}</strong>`);
                }
            },


            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000, 2000],
                [10, 25, 50, 100, 500, 1000, 2000]
            ],
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });


        var cancelled_table = $('#cancelled-monthly-forms-table').DataTable({
            ajax: {
                url: "{{ route('monthly-forms.data') }}",
                data: function(d) {
                    d.status = 'cancelled';
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.department = $('#department-filter').val();
                    d.follow_up_department = $('#follow-up-department-filter').val();
                    d.employee = $('#employee-filter').val();
                    
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
                    data: 'cancellation_reason',
                    name: 'cancellation_reason'
                },
                {
                    data: 'cancellation_date',
                    name: 'cancellation_date'
                },
                {
                    data: 'area',
                    name: 'area'
                },
                // {
                //     data: 'address',
                //     name: 'address'
                // },
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
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Cancelled Monthly Forms Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Cancelled Monthly Forms Data', 
                //     exportOptions: {
                //         columns: [0, 1]
                //     }
                // },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });



        $('#department-filter').on('change', function() {
            let departmentId = $(this).val();

            if (departmentId === 'all') {
                $('#employee-filter').html(`<option value="all">{{__('All')}}</option>`);
                return;
            }

            $.ajax({
                url: '{{ route("employee.getEmployeesByDepartment") }}', // Adjust route as needed
                type: 'GET',
                data: {
                    department_id: departmentId
                },
                success: function(response) {
                    if (response.success) {
                        let options = `<option value="all">{{__('All')}}</option>`;
                        response.data.forEach(employee => {
                            options += `<option value="${employee.id}">${employee.name}</option>`;
                        });
                        $('#employee-filter').html(options);
                    }
                }
            });

            monthlyFormsTable.ajax.reload();


        });


        // Date filter change
        $('#date-filter').on('change', function() {
            if ($(this).val() === 'range') {
                $('#custom-range, #end-date-container').show();
            } else {
                $('#custom-range, #end-date-container').hide();
                $('#start-date, #end-date').val('');
            }
            monthlyFormsTable.ajax.reload();
            cancelled_table.ajax.reload();
        });


        // Start date and end date change
        $('#start-date, #end-date').on('change', function() {
            monthlyFormsTable.ajax.reload();
            cancelled_table.ajax.reload();
        });

        $('#employee-filter').on('change', function() {
            monthlyFormsTable.ajax.reload();
            cancelled_table.ajax.reload();

        });

        $('#follow-up-department-filter').on('change', function() {
            monthlyFormsTable.ajax.reload();
            cancelled_table.ajax.reload();


        });



        $('#clear-filters').on('click', function() {
            $('#date-filter').val('all').trigger('change');
            $('#department-filter').val('all').trigger('change');
            $('#follow-up-department-filter').val('all').trigger('change');
            $('#employee-filter').html('<option value="all">All</option>');
            $('#start-date, #end-date').val('');
            monthlyFormsTable.ajax.reload();
            cancelled_table.ajax.reload();

        });



        // let financialRowIndex = 0;
        // let inKindRowIndex = 0;

        // // Add row for Financial Donations
        // $(document).on('click', '.add-row-btn', function() {
        //     const container = $($(this).data('target'));
        //     if (container.attr('id') === 'financial-donation-rows-container' || container.attr('id') === 'edit-financial-donation-rows-container') {
        //         financialRowIndex++;
        //         let newIndex = 0;
        //         while (existingFinancialIndices.has(newIndex)) {
        //             newIndex++;
        //         }
        //         existingFinancialIndices.add(newIndex); // Track the new index
        //         container.append(`
        //     <div class="row donation-row">
        //         <input type="hidden" name="items[${newIndex}][financial_donation_type]" value="financial">

        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
        //                 <select class="form-control donation-category" name="items[${newIndex}][financial_donation_categories_id]">
        //                     <option value="">{{__('Select Category')}}</option>
        //                     @foreach($donationCategories as $category)
        //                         <option value="{{ $category->id }}">{{ $category->name }}</option>
        //                     @endforeach
        //                 </select>
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>
        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="amount" class="form-label">{{__('Amount')}}</label>
        //                 <input type="number" class="form-control amount" name="items[${newIndex}][financial_amount]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>

        //         <div class="col-md-4 d-flex align-items-center">
        //             <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
        //         </div>
        //     </div>
        // `);
        //     } else if (container.attr('id') === 'in-kind-donation-rows-container' || container.attr('id') === 'edit-in-kind-donation-rows-container') {

        //         // Generate a unique index for in-kind donations
        //         let newIndex = 0;
        //         while (existingInKindIndices.has(newIndex)) {
        //             newIndex++;
        //         }
        //         existingInKindIndices.add(newIndex); // Track the new index
        //         container.append(`
        //     <div class="row donation-row">
        //         <input type="hidden" name="items[${newIndex}][inKind_donation_type]" value="inKind">
        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="item_name" class="form-label">{{__('Item Name')}}</label>
        //                 <input type="text" class="form-control" name="items[${newIndex}][in_kind_item_name]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>
        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="quantity" class="form-label">{{__('Quantity')}}</label>
        //                 <input type="number" class="form-control" name="items[${newIndex}][in_kind_quantity]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>
        //         <div class="col-md-4 d-flex align-items-center">
        //             <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
        //         </div>
        //     </div>
        // `);
        //     }
        // });


        let financialRowIndex = 0;
        let inKindRowIndex = 0;

        // Add row for Financial Donations
        $(document).on('click', '.add-row-btn', function() {
            const container = $($(this).data('target'));

            if (container.attr('id') === 'financial-donation-rows-container' || container.attr('id') === 'edit-financial-donation-rows-container') {
                let newIndex = financialRowIndex;
                while (existingFinancialIndices.has(newIndex)) {
                    newIndex++;
                }
                existingFinancialIndices.add(newIndex);
                financialRowIndex = newIndex + 1;

                const financialRow = `
        <div class="row donation-row">
            <input type="hidden" name="items[${newIndex}][financial_donation_type]" value="financial">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Category')}}</label>
                    <select class="form-control donation-category" name="items[${newIndex}][financial_donation_categories_id]">
                       ${donationCategoryOptions}
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">{{__('Amount')}}</label>
                    <input type="number" class="form-control amount" name="items[${newIndex}][financial_amount]">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit" data-index="${newIndex}" data-type="financial">{{__('Remove')}}</button>
            </div>
        </div>
        `;
                container.append(financialRow);
            }

            // Add row for In-Kind Donations
            else if (container.attr('id') === 'in-kind-donation-rows-container' || container.attr('id') === 'edit-in-kind-donation-rows-container') {
                let newIndex = inKindRowIndex;
                while (existingInKindIndices.has(newIndex)) {
                    newIndex++;
                }
                existingInKindIndices.add(newIndex);
                inKindRowIndex = newIndex + 1;

                const inKindRow = `
        <div class="row donation-row">
            <input type="hidden" name="items[${newIndex}][inKind_donation_type]" value="inKind">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                    <input type="text" class="form-control" name="items[${newIndex}][in_kind_item_name]">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="quantity" class="form-label">{{__('Amount')}}</label>
                    <input type="number" class="form-control" name="items[${newIndex}][in_kind_quantity]">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit" data-index="${newIndex}" data-type="inKind">{{__('Remove')}}</button>
            </div>
        </div>
        `;
                container.append(inKindRow);
            }
        });


        // Remove Row
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('.row').remove();
        });

        // Handle Remove Row buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row-btn')) {
                e.target.closest('.donation-row').remove();
            }
        });


        // Remove Row Edit with SweetAlert Confirmation
        $(document).on('click', '.remove-row-btn-edit', function() {
            const row = $(this).closest('.donation-row');
            const donationId = row.find('input[name*="_monthly_donation_id"]').val(); // Extract donation ID

            if (!donationId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Donation ID not found.'
                });
                return;
            }

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
                        url: `{{ url('monthly-forms/delete-item') }}/${donationId}`, // Endpoint to handle deletion
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
        });


        // Add Monthly Form Form Submit
        $('#addMonthlyFormForm').on('submit', function(e) {
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
                        $('#addMonthlyFormModal').modal('hide');
                        form[0].reset();
                        monthlyFormsTable.ajax.reload();
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
                            title: "{{ __('validation.Validation Error ') }}", // Ensure this is rendered as a string by Blade
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


        // Edit Monthly Form Form Submit
        $('#editMonthlyFormForm').on('submit', function(e) {
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
                        $('#editMonthlyFormModal').modal('hide');
                        form[0].reset();
                        monthlyFormsTable.ajax.reload();
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
                        let errors = response.errors || [];
                        let errorDetails = '';

                        if (Array.isArray(errors)) {
                            // Handle row-specific errors
                            errorDetails = errors.map(error =>
                                `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');
                        } else {
                            // Handle general file validation error
                            errorDetails = errors.file ? errors.file[0] : 'Something went wrong.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: `<pre style="direction: ltr;">${errorDetails}</pre>`,
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
        // Clear form validation on modal hide


        $('.modal').on('hidden.bs.modal', function() {
            var form = $(this).find('form');
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });



        // import monthly form submit
        $('#importMonthlyFormForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            // Clear previous messages
            $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

            $.ajax({
                url: "{{ route('monthly-forms.import-forms') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#importMonthlyFormForm button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: response.message,
                        });

                        if (response.errors && response.errors.length > 0) {
                            let errorDetails = response.errors.map(error =>
                                `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');

                            Swal.fire({
                                icon: 'info',
                                title: 'Some Records Skipped',
                                html: `<pre>${errorDetails}</pre>`,
                                customClass: {
                                    popup: 'text-start',
                                }
                            });
                        }

                        setTimeout(() => {
                            $('#importMonthlyFormModal').modal('hide');
                            $('#importMonthlyFormForm')[0].reset();
                        }, 2000);

                        monthlyFormsTable.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed',
                            text: response.message || 'An error occurred.',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unexpected Error',
                        text: xhr.responseJSON?.error || 'An unexpected error occurred. Please try again later.',
                    });
                },
                complete: function() {
                    $('#importMonthlyFormForm button[type="submit"]').prop('disabled', false);
                }
            });
        });


        // import monthly form items submit
        $('#importMonthlyFormItemForm').off('submit').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            let formData = new FormData(this);

            // Clear previous messages
            $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

            // AJAX request
            $.ajax({
                url: "{{ route('monthly-forms.import-items') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#importMonthlyFormItemForm button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: response.message,
                        });

                        if (response.errors && response.errors.length > 0) {
                            let errorDetails = response.errors.map(error =>
                                `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');

                            Swal.fire({
                                icon: 'info',
                                title: 'Some Records Skipped',
                                html: `<pre>${errorDetails}</pre>`,
                                customClass: {
                                    popup: 'text-start',
                                }
                            });
                        }

                        // Close modal and reset form
                        setTimeout(() => {
                            $('#importMonthlyFormItemModal').modal('hide');
                            $('#importMonthlyFormItemForm')[0].reset();
                        }, 2000);

                        monthlyFormsTable.ajax.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation error
                        let response = xhr.responseJSON;
                        let errors = response.errors || [];
                        let errorDetails = '';

                        if (Array.isArray(errors)) {
                            // Handle row-specific errors
                            errorDetails = errors.map(error =>
                                `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');
                        } else {
                            // Handle general file validation error
                            errorDetails = errors.file ? errors.file[0] : 'Something went wrong.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: `<pre style="direction: ltr;">${errorDetails}</pre>`,
                            customClass: {
                                popup: 'text-start',
                            }
                        });
                    } else {
                        // General error
                        Swal.fire({
                            icon: 'error',
                            title: 'Unexpected Error',
                            text: 'An unexpected error occurred. Please try again later.',
                        });
                    }
                },
                complete: function() {
                    $('#importMonthlyFormItemForm button[type="submit"]').prop('disabled', false);
                }
            });
        });

    });



    // add monthly form submit
    $(document).ready(function() {
        $('#donor_id').select2({
            dropdownParent: $('#addMonthlyFormModal'),
            placeholder: "{{__('Search by ID or Phone')}}",
            ajax: {
                url: '{{ route("donors.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        query: params.term, // Search query
                        monthly: true
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

        $('#edit_donor_id').select2({
            dropdownParent: $('#editMonthlyFormModal'),
            placeholder: "{{__('Search by ID or Phone')}}",
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

    });


    let existingFinancialIndices = new Set();
    let existingInKindIndices = new Set();

    const donationCategoryOptions = donationCategories.map(category => `
        <option value="${category.id}">${category.name}</option>
        `).join('');


    function editMonthlyForm(id) {
        var form = $('#editMonthlyFormForm');
        form.trigger('reset');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        // **Show Loading Spinner & Disable Form Inputs**
        $('#editMonthlyFormLoader').removeClass('d-none'); // Show loading spinner
        form.find('input, select, textarea, button').prop('disabled', true); // Disable inputs

        form.attr('action', `{{ route('monthly-forms.update', '') }}/${id}`);
        $('#editMonthlyFormModal').modal('show');

        // Reset indices tracking
        existingFinancialIndices = new Set();
        existingInKindIndices = new Set();


        $.ajax({
            url: `{{ url('monthly-forms') }}/${id}/edit`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Ensures Laravel returns JSON instead of redirecting
            },
            success: function(data) {
                $('#edit_donor_id').val(data.donor_id).trigger('change');
                $('#edit_donation_type').val(data.donation_type).trigger('change');
                $('#edit_id').val(data.id);
                $('#edit_collecting_donation_way').val(data.collecting_donation_way);
                $('#edit_monthly_donation_status').val(data.status);
                $('#edit_cancellation_reason').val(data.cancellation_reason);
                $('#edit_cancellation_date').val(data.cancellation_date);
                $('#edit_department_id').val(data.department_id);
                $('#edit_employee_id').val(data.employee_id);
                $('#edit_notes').val(data.notes);
                $('#edit_form_date').val(data.form_date);
                $('#edit_follow_up_department_id').val(data.follow_up_department_id);


                const reasonContainer = document.getElementById('edit-reason-container');
                const dateContainer = document.getElementById('edit-date-container');
                if (data.status === 'cancelled') {
                    reasonContainer.style.display = 'block';
                    dateContainer.style.display = 'block';
                } else {
                    reasonContainer.style.display = 'none';
                    dateContainer.style.display = 'none';
                }

                const financialContainer = $('#edit-financial-donation-rows-container');
                const inKindContainer = $('#edit-in-kind-donation-rows-container');

                const financialSection = document.getElementById('edit-financial-donations-section');
                const inKindSection = document.getElementById('edit-in-kind-donations-section');

                if (financialContainer.length === 0 || inKindContainer.length === 0) {
                    console.error("Required containers not found.");
                    return;
                }

                // Handle both financial and in-kind donations
                if (data.donation_type === 'both') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.remove('d-none');

                    // Handle Financial Donations
                    const financialDonations = data.items.filter(donation => donation.donation_type === 'financial');
                    if (financialDonations.length > 0) {
                        financialContainer.empty(); // Clear only if there is data
                        financialDonations.forEach((donation, index) => {
                            existingFinancialIndices.add(index);
                            const financialRow = `
                        <div class="row donation-row">
                           <input type="hidden" name="items[${index}][id]" value="financial">

                            <input type="hidden" name="items[${index}][financial_donation_type]" value="financial">
                            <input type="hidden" name="items[${index}][financial_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="items[${index}][financial_donation_categories_id]">
                                       ${donationCategoryOptions}
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="items[${index}][financial_amount]" value="${donation.amount}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
                            </div>
                        </div>
                    `;
                            financialContainer.append(financialRow);
                        });
                    }

                    // Handle In-Kind Donations
                    const inKindDonations = data.items.filter(donation => donation.donation_type === 'inKind');
                    if (inKindDonations.length > 0) {
                        inKindContainer.empty(); // Clear only if there is data
                        inKindDonations.forEach((donation, index) => {
                            existingInKindIndices.add(index);
                            const inKindRow = `
                        <div class="row donation-row">
                            <input type="hidden" name="items[${index}][inKind_donation_type]" value="inKind">
                            <input type="hidden" name="items[${index}][inkind_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="items[${index}][in_kind_item_name]" value="${donation.item_name}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control" name="items[${index}][in_kind_quantity]" value="${donation.amount}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
                            </div>
                        </div>
                    `;
                            inKindContainer.append(inKindRow);
                        });
                    }
                } else if (data.donation_type === 'financial') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.add('d-none');

                    // Handle Financial Donations
                    const financialDonations = data.items.filter(donation => donation.donation_type === 'financial');
                    if (financialDonations.length > 0) {
                        financialContainer.empty(); // Clear only if there is data
                        financialDonations.forEach((donation, index) => {
                            existingFinancialIndices.add(index);
                            const financialRow = `
                        <div class="row donation-row">
                            <input type="hidden" name="items[${index}][financial_donation_type]" value="financial">
                            <input type="hidden" name="items[${index}][financial_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="items[${index}][financial_donation_categories_id]">
                                       ${donationCategoryOptions}
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="items[${index}][financial_amount]" value="${donation.amount}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
                            </div>
                        </div>
                    `;
                            financialContainer.append(financialRow);
                        });
                    }
                } else if (data.donation_type === 'inKind') {
                    financialSection.classList.add('d-none');
                    inKindSection.classList.remove('d-none');

                    // Handle In-Kind Donations
                    const inKindDonations = data.items.filter(donation => donation.donation_type === 'inKind');
                    if (inKindDonations.length > 0) {
                        inKindContainer.empty(); // Clear only if there is data
                        inKindDonations.forEach((donation, index) => {
                            existingInKindIndices.add(index);
                            const inKindRow = `
                        <div class="row donation-row">
                            <input type="hidden" name="items[${index}][inKind_donation_type]" value="inKind">
                            <input type="hidden" name="items[${index}][inkind_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="items[${index}][in_kind_item_name]" value="${donation.item_name}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control" name="items[${index}][in_kind_quantity]" value="${donation.amount}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-danger mt-2 remove-row-btn-edit">{{__('Remove')}}</button>
                            </div>
                        </div>
                    `;
                            inKindContainer.append(inKindRow);
                        });
                    }
                }

            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('{{ __("Failed to load monthly form details. Please try again.") }}');
            },
            complete: function() {
                console.log('Monthly form loaded successfully');
                // Re-enable form inputs
                form.find('input, select, textarea, button').prop('disabled', false);

                // Hide loading spinner
                $('#editMonthlyFormLoader').addClass('d-none');
            }

        });
    }


    document.getElementById('monthly_donation_status').addEventListener('change', function() {
        const reasonContainer = document.getElementById('reason-container');
        const dateContainer = document.getElementById('date-container');
        if (this.value === 'cancelled') {
            reasonContainer.style.display = 'block';
            dateContainer.style.display = 'block';
        } else {
            reasonContainer.style.display = 'none';
            dateContainer.style.display = 'none';
        }
    });

    document.getElementById('edit_monthly_donation_status').addEventListener('change', function() {
        const reasonContainer = document.getElementById('edit-reason-container');
        const dateContainer = document.getElementById('edit-date-container');
        if (this.value === 'cancelled') {
            reasonContainer.style.display = 'block';
            dateContainer.style.display = 'block';
        } else {
            reasonContainer.style.display = 'none';
            dateContainer.style.display = 'none';
        }
    });



    function toggleDonationType() {
        const donationType = document.getElementById('donation_type').value;
        const financialContainer = document.getElementById('financial-donations-section');
        const inKindContainer = document.getElementById('in-kind-donations-section');


        if (donationType === 'financial') {
            financialContainer.classList.remove('d-none');
            inKindContainer.classList.add('d-none');
        } else if (donationType === 'inKind') {
            financialContainer.classList.add('d-none');
            inKindContainer.classList.remove('d-none');
        } else {
            financialContainer.classList.remove('d-none');
            inKindContainer.classList.remove('d-none');
        }
    }

    function toggleEditDonationType() {
        const donationType = document.getElementById('edit_donation_type').value;
        const editFinancialContainer = document.getElementById('edit-financial-donations-section');
        const editInKindContainer = document.getElementById('edit-in-kind-donations-section');

        if (donationType === 'financial') {
            editFinancialContainer.classList.remove('d-none');
            editInKindContainer.classList.add('d-none');
        } else if (donationType === 'inKind') {
            editFinancialContainer.classList.add('d-none');
            editInKindContainer.classList.remove('d-none');
        } else {
            editFinancialContainer.classList.remove('d-none');
            editInKindContainer.classList.remove('d-none');
        }
    }



    // monthly form details
    function monthlyFormDetails(id) {
        $('#detailsMonthlyFormModal').modal('show');

        $.get(`{{ url('monthly-forms') }}/${id}/details`)
            .done(function(data) {
                // Construct the content to be displayed in the modal
                let modalContent = `
                    <h4 class="text-primary">{{__('Donor Information')}}</h4>
                    <div class="row mb-3">
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
          

            <h4 class="text-danger">{{__('Monthly Form Information')}}</h4>
             <div class="row">
            
                 <div class="col-md-3">
                    <p><strong>{{__('Status')}}:</strong> 
                    ${data.status === 'ongoing' ? '{{__("Ongoing")}}' : '{{__("Cancelled")}}' }</p>
                 </div>
                 <div class="col-md-3">
                   <p><strong>{{__('Donation Type')}}:</strong>
                    ${data.donation_type === 'financial' ? '{{__("Financial")}}' : data.donation_type === 'inKind' ? '{{__("In Kind")}}' : '{{__("Both")}}'}
                    </p>
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
                    <p><strong>{{__('Notes')}}:</strong> ${data.notes || 'N/A'}</p>
                  </div>
                  </div>
       `;

                // Financial Donations Table
                if (data.donation_type === 'financial' || data.donation_type === 'both') {
                    modalContent += `
                <h4 class="text-warning">{{__('Financial Donations')}}</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{__('Donation Category')}}</th>
                            <th>{{__('Amount')}}</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    data.items
                        .filter(item => item.donation_type === 'financial')
                        .forEach(item => {
                            modalContent += `
                        <tr>
                            <td>${item.donation_category.name}</td>
                            <td>${item.amount}</td>
                        </tr>`;
                        });

                    modalContent += `
                    </tbody>
                </table>`;
                }

                // In-Kind Donations Table
                if (data.donation_type === 'inKind' || data.donation_type === 'both') {
                    modalContent += `
                <h4 class="text-warning">{{__('In-Kind Donations')}}</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{__('Item Name')}}</th>
                            <th>{{__('Quantity')}}</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    data.items
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

                // Add the constructed content to the modal body
                $('#detailsMonthlyFormModal .modal-body').html(modalContent);

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



    // keyboard keys
    document.addEventListener('keydown', function(event) {
        // Check if Ctrl (or Cmd on Mac) is pressed
        if (event.ctrlKey || event.metaKey) {
            // Prevent the default behavior (if needed)
            event.preventDefault();

            // Check for F1 key
            if (event.key === 'F1') {
                $('#addMonthlyFormModal').modal('show'); // Open the "Add Donor" modal
            }
        }
        if (event.key === 'F2') {
            // Check if the "Add Monthly Form" modal is open
            if ($('#addMonthlyFormModal').is(':visible')) {
                $('#addMonthlyFormForm').submit(); // Submit the "Add" form
            }
            // Check if the "Edit Monthly Form" modal is open
            else if ($('#editMonthlyFormModal').is(':visible')) {
                $('#editMonthlyFormForm').submit(); // Submit the "Edit" form
            }

        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.key === 'c') {
            event.stopPropagation(); // Prevent DataTables from blocking copy action
        }
        if (event.ctrlKey && event.key === 'v') {
            event.stopPropagation(); // Prevent DataTables from blocking copy action
        }
    }, true);


    $('#importMonthlyFormForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        // Clear previous messages
        $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

        $.ajax({
            url: "{{ route('monthly-forms.import-forms') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#importMonthlyFormForm button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful',
                        text: response.message,
                    });

                    if (response.errors && response.errors.length > 0) {
                        let errorDetails = response.errors.map(error =>
                            `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');

                        Swal.fire({
                            icon: 'info',
                            title: 'Some Records Skipped',
                            html: `<pre>${errorDetails}</pre>`,
                            customClass: {
                                popup: 'text-start',
                            }
                        });
                    }

                    setTimeout(() => {
                        $('#importMonthlyFormModal').modal('hide');
                        $('#importMonthlyFormForm')[0].reset();
                    }, 2000);

                    monthlyFormsTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed',
                        text: response.message || 'An error occurred.',
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: xhr.responseJSON?.error || 'An unexpected error occurred. Please try again later.',
                });
            },
            complete: function() {
                $('#importMonthlyFormForm button[type="submit"]').prop('disabled', false);
            }
        });
    });


    $('#importMonthlyFormItemForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        let formData = new FormData(this);

        // Clear previous messages
        $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

        // AJAX request
        $.ajax({
            url: "{{ route('monthly-forms.import-items') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#importMonthlyFormItemForm button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful',
                        text: response.message,
                    });

                    if (response.errors && response.errors.length > 0) {
                        let errorDetails = response.errors.map(error =>
                            `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');

                        Swal.fire({
                            icon: 'info',
                            title: 'Some Records Skipped',
                            html: `<pre>${errorDetails}</pre>`,
                            customClass: {
                                popup: 'text-start',
                            }
                        });
                    }

                    // Close modal and reset form
                    setTimeout(() => {
                        $('#importMonthlyFormItemModal').modal('hide');
                        $('#importMonthlyFormItemForm')[0].reset();
                    }, 2000);

                    monthlyFormsTable.ajax.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation error
                    let response = xhr.responseJSON;
                    let errors = response.errors || [];
                    let errorDetails = '';

                    if (Array.isArray(errors)) {
                        // Handle row-specific errors
                        errorDetails = errors.map(error =>
                            `Row ${error.row}: ${error.errors.join(', ')}`).join('\n');
                    } else {
                        // Handle general file validation error
                        errorDetails = errors.file ? errors.file[0] : 'Something went wrong.';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: `<pre style="direction: ltr;">${errorDetails}</pre>`,
                        customClass: {
                            popup: 'text-start',
                        }
                    });
                } else {
                    // General error
                    Swal.fire({
                        icon: 'error',
                        title: 'Unexpected Error',
                        text: 'An unexpected error occurred. Please try again later.',
                    });
                }
            },
            complete: function() {
                $('#importMonthlyFormItemForm button[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>
@endpush