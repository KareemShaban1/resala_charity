@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
               
                <h4 class="page-title">{{__('Collecting Line')}}</h4>
            </div>

          

        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <table id="collecting-line-donations-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Donor Name')}}</th>
                                <th>{{__('Area')}}</th>
                                <th>{{__('Phones')}}</th>
                                <th>{{__('Monthly Form Day')}}</th>
                                <th>{{__('Collected')}}</th>
                                <th>{{__('Donates')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated via DataTables -->
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <!-- Edit Donation Modal -->
    @include('backend.pages.donations.edit_modal')
</div>

@push('scripts')
<script>
    var donationCategories = @json($donationCategories);
    var collectingLine = @json($collectingLine);
    var viewDonationsTable = $('#collecting-line-donations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('collecting-lines.donations.data') }}",
            data: function(d) {
                // // Retrieve the collectingLine ID from the modal's data attribute
                d.collecting_line_id = collectingLine.id;
                // console.log('Sending Collecting Line ID:', d.collecting_line_id); // Debugging: Verify the ID being sent
                // // Additional filters (if needed)
                // d.date = $('#date').val();
                // d.area_group = $('#area_group').val();
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
            },
            {
                data: 'actions',
                name: 'actions',
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
                $('#edit_collecting_way').val(data.collecting_donation?.collecting_way).trigger('change');

                // Populate financial donations
                const financialContainer = $('#edit-financial-donation-rows-container');
                financialContainer.empty();
                data.donate_items
                    .filter(item => item.donation_type === 'financial')
                    .forEach((donationItem, index) => {
                        existingFinancialIndices.add(index); // Track existing indices
                        financialContainer.append(renderFinancialRow(donationItem, index, donationCategories));
                    });

                // Populate in-kind donations
                const inKindContainer = $('#edit-in-kind-donation-rows-container');
                inKindContainer.empty();
                data.donate_items
                    .filter(item => item.donation_type === 'inKind')
                    .forEach((donationItem, index) => {
                        existingInKindIndices.add(index); // Track existing indices
                        inKindContainer.append(renderInKindRow(donationItem, index));
                    });

                // Toggle sections based on donation type
                toggleEditDonationType();
                toggleEditDonationStatus();
            })
            .fail(function() {
                alert('{{ __("Failed to load donation details. Please try again.") }}');
            });
    }


    function renderFinancialRow(donationItem, index, categories) {
        console.log(donationItem);
        const categoryOptions = categories.map(category =>
            `<option value="${category.id}" ${category.id == donationItem.donation_category_id ? 'selected' : ''}>${category.name}</option>`
        ).join('');

        return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][financial_donation_type]" value="financial">
            <input type="hidden" name="donates[${index}][financial_donation_item_id]" value="${donationItem.id || ''}">
            <div class="col-md-3">
                <label class="form-label">{{__('Donation Category')}}</label>
                <select class="form-control" name="donates[${index}][financial_donation_categories_id]">
                    ${categoryOptions}
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{__('Amount')}}</label>
                <input type="number" class="form-control" name="donates[${index}][financial_amount]" value="${donationItem.amount || ''}">
                <div class="invalid-feedback"></div>
            </div>
             <div class="col-md-3">
                <label class="form-label">{{__('Financial Receipt Number')}}</label>
                <input type="text" class="form-control" name="donates[${index}][financial_receipt_number]" value="${donationItem.financial_receipt_number || ''}">
                <div class="invalid-feedback"></div>
            </div>
             <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][financial_donation_item_type]">
                        <option value="normal" ${donationItem.donation_item_type === 'normal' ? 'selected' : ''}>{{__('Normal')}}</option>
                        <option value="monthly" ${donationItem.donation_item_type === 'monthly' ? 'selected' : ''}>{{__('Monthly')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
            </div>
        </div>`;
    }

    function renderInKindRow(donationItem, index) {
        console.log(donationItem);
        return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][inKind_donation_type]" value="inKind">
            <input type="hidden" name="donates[${index}][in_kind_donation_item_id]" value="${donationItem.id || ''}">
            <div class="col-md-3">
                <label class="form-label">{{__('Item Name')}}</label>
                <input type="text" class="form-control" name="donates[${index}][in_kind_item_name]" value="${donationItem.item_name || ''}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{__('Quantity')}}</label>
                <input type="number" class="form-control" name="donates[${index}][in_kind_quantity]" value="${donationItem.amount || ''}">
                <div class="invalid-feedback"></div>
            </div>
             <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${index}][in_kind_donation_item_type]">
                        <option value="normal" ${donationItem.donation_item_type === 'normal' ? 'selected' : ''}>{{__('Normal')}}</option>
                        <option value="monthly" ${donationItem.donation_item_type === 'monthly' ? 'selected' : ''}>{{__('Monthly')}}</option>
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
                        donationTable.ajax.reload();

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
                    viewDonationsTable.ajax.reload();
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

    // Helper function to format date in YYYY-MM-DD
    function formatDate(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

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
                
                <div class="col-md-2">
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
                 <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${newIndex}][financial_donation_item_type]">
                        <option value="normal">{{__('Normal')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
                <div class="col-md-1 d-flex align-items-center">
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
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                        <input type="text" class="form-control" name="donates[${newIndex}][in_kind_item_name]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                        <input type="number" class="form-control" name="donates[${newIndex}][in_kind_quantity]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                 <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">{{__('Donation Item Type')}}</label>
                    <select class="form-control" name="donates[${newIndex}][in_kind_donation_item_type]">
                        <option value="normal">{{__('Normal')}}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
                <div class="col-md-3 d-flex align-items-center">
                    <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
                </div>
            </div>
        `);
        }
    });
</script>
@endpush
@endsection