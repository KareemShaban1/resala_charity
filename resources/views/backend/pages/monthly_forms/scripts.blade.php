@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        // Initialize DataTable
        var table = $('#monthly-forms-table').DataTable({
            ajax: {
                url: "{{ route('monthly-forms.data') }}",
                data: function(d) {
                    d.status = 'ongoing'; // Add the status parameter
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
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Monthly Forms Data', 
                //     exportOptions: {
                //         columns: [0, 1]
                //     }
                // },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
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


        var cancelled_table = $('#cancelled-monthly-forms-table').DataTable({
            ajax: {
                url: "{{ route('monthly-forms.data') }}",
                data: function(d) {
                    d.status = 'cancelled'; // Add the status parameter
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

        let financialRowIndex = 0;
        let inKindRowIndex = 0;

        // Add row for Financial Donations
        $(document).on('click', '.add-row-btn', function() {
            const container = $($(this).data('target'));
            if (container.attr('id') === 'financial-donation-rows-container' || container.attr('id') === 'edit-financial-donation-rows-container') {
                financialRowIndex++;
                container.append(`
            <div class="row donation-row">
                <input type="hidden" name="items[${financialRowIndex}][financial_donation_type]" value="financial">
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                        <select class="form-control donation-category" name="items[${financialRowIndex}][financial_donation_categories_id]">
                            <option value="">{{__('Select Category')}}</option>
                            @foreach($donationCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="amount" class="form-label">{{__('Amount')}}</label>
                        <input type="number" class="form-control amount" name="items[${financialRowIndex}][financial_amount]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-center">
                    <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
                </div>
            </div>
        `);
            } else if (container.attr('id') === 'in-kind-donation-rows-container' || container.attr('id') === 'edit-in-kind-donation-rows-container') {
                inKindRowIndex++;
                container.append(`
            <div class="row donation-row">
                <input type="hidden" name="items[${inKindRowIndex}][inKind_donation_type]" value="inKind">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                        <input type="text" class="form-control" name="items[${inKindRowIndex}][in_kind_item_name]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                        <input type="number" class="form-control" name="items[${inKindRowIndex}][in_kind_quantity]">
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

    });



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


    function editMonthlyForm(id) {
    var form = $('#editMonthlyFormForm');
    form.trigger('reset');
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').text('');
    form.attr('action', `{{ route('monthly-forms.update', '') }}/${id}`);
    $('#editMonthlyFormModal').modal('show');

    $.get(`{{ url('monthly-forms') }}/${id}/edit`, function(data) {
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
                    const financialRow = `
                        <div class="row donation-row">
                           <input type="hidden" name="items[${index}][id]" value="financial">

                            <input type="hidden" name="items[${index}][financial_donation_type]" value="financial">
                            <input type="hidden" name="items[${index}][financial_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="items[${index}][financial_donation_categories_id]">
                                        ${donationCategories.map(category => `
                                            <option value="${category.id}" ${Number(category.id) === Number(donation.donation_category_id || 0) ? 'selected' : ''}>
                                                ${category.name}
                                            </option>
                                        `).join('')}
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
                    const financialRow = `
                        <div class="row donation-row">
                            <input type="hidden" name="items[${index}][financial_donation_type]" value="financial">
                            <input type="hidden" name="items[${index}][financial_monthly_donation_id]" value="${donation.id}">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="items[${index}][financial_donation_categories_id]">
                                        ${donationCategories.map(category => `
                                            <option value="${category.id}" ${Number(category.id) === Number(donation.donation_category_id || 0) ? 'selected' : ''}>
                                                ${category.name}
                                            </option>
                                        `).join('')}
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

    }).fail(function(error) {
        console.error('Error fetching data:', error);
        alert('Failed to fetch data. Please try again.');
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
                    <p><strong>{{__('Notes')}}:</strong> ${data.notes}</p>
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
</script>
@endpush