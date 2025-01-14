@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        // Initialize DataTable
        var table = $('#donations-table').DataTable({
            ajax: {
                url: "{{ route('donations.data') }}",
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
                    data: 'receipt_number',
                    name: 'receipt_number'

                },

                {
                    data: 'donateItems', // Add the 'donates' column
                    name: 'donateItems',
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
                    title: 'Donations Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Donations Data', 
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
                <input type="hidden" name="donates[${financialRowIndex}][financial_donation_type]" value="financial">
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                        <select class="form-control donation-category" name="donates[${financialRowIndex}][financial_donation_categories_id]">
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
                        <input type="number" class="form-control amount" name="donates[${financialRowIndex}][financial_amount]">
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
                <input type="hidden" name="donates[${inKindRowIndex}][inKind_donation_type]" value="inKind">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                        <input type="text" class="form-control" name="donates[${inKindRowIndex}][in_kind_item_name]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                        <input type="number" class="form-control" name="donates[${inKindRowIndex}][in_kind_quantity]">
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
                            table.ajax.reload();

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


        // Add Donation Form Submit
        $('#addDonationForm').on('submit', function(e) {
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
                        $('#addDonationModal').modal('hide');
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
        // Clear form validation on modal hide


        $('.modal').on('hidden.bs.modal', function() {
            var form = $(this).find('form');
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });

    });



    $(document).ready(function() {
        $('#donor_id').select2({
            dropdownParent: $('#addDonationModal'),
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




    });


    function toggleDonationType() {
        const donationType = document.getElementById('donation_type').value;
        const financialContainer = document.getElementById('financial-donations-section');
        const inKindContainer = document.getElementById('in-kind-donations-section');
        const financialReceiptConatiner = document.getElementById('financial-receipt-container');
        const inKindReceiptConatiner = document.getElementById('in-kind-receipt-container');

        if (donationType === 'financial') {
            financialContainer.classList.remove('d-none');
            inKindContainer.classList.add('d-none');
            financialReceiptConatiner.classList.remove('d-none');
            inKindReceiptConatiner.classList.add('d-none');
        } else if (donationType === 'inKind') {
            financialContainer.classList.add('d-none');
            inKindContainer.classList.remove('d-none');
            financialReceiptConatiner.classList.add('d-none');
            inKindReceiptConatiner.classList.remove('d-none');
        } else {
            financialContainer.classList.remove('d-none');
            inKindContainer.classList.remove('d-none');
            financialReceiptConatiner.classList.remove('d-none');
            inKindReceiptConatiner.classList.remove('d-none');
        }
    }
    function toggleDonationStatus() {
        const donationStatus = document.getElementById('donation_status').value;
        const donationType = document.getElementById('donation_type').value;
        const CollectingSection = document.getElementById('collecting-section');
        const financialReceiptConatiner = document.getElementById('financial-receipt-container');
        const inKindReceiptConatiner = document.getElementById('in-kind-receipt-container');
        if (donationStatus === 'collected') {
            CollectingSection.classList.remove('d-none');
        } else if (donationStatus === 'not_collected') {
            CollectingSection.classList.add('d-none');
        }

        if (donationType === 'financial') {
            financialReceiptConatiner.classList.remove('d-none');
            inKindReceiptConatiner.classList.add('d-none');
        } else if (donationType === 'inKind') {
            financialReceiptConatiner.classList.add('d-none');
            inKindReceiptConatiner.classList.remove('d-none');
        } else {
            financialReceiptConatiner.classList.remove('d-none');
            inKindReceiptConatiner.classList.remove('d-none');
        }

    }


    function toggleEditDonationType() {
        const donationType = document.getElementById('edit_donation_type').value;
        const editFinancialContainer = document.getElementById('edit-financial-donations-section');
        const editInKindContainer = document.getElementById('edit-in-kind-donations-section');
        const financialReceiptConatiner = document.getElementById('edit-financial-receipt-container');
        const inKindReceiptConatiner = document.getElementById('edit-in-kind-receipt-container');

        if (donationType === 'financial') {
            editFinancialContainer.classList.remove('d-none');
            editInKindContainer.classList.add('d-none');
            financialReceiptConatiner.classList.remove('d-none');
            inKindReceiptConatiner.classList.add('d-none');
        } else if (donationType === 'inKind') {
            editFinancialContainer.classList.add('d-none');
            editInKindContainer.classList.remove('d-none');
            financialReceiptConatiner.classList.add('d-none');
            inKindReceiptConatiner.classList.remove('d-none');
        } else {
            editFinancialContainer.classList.remove('d-none');
            editInKindContainer.classList.remove('d-none');
            financialReceiptConatiner.classList.remove('d-none');
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

    $(document).ready(function() {
        // Initialize event listeners for adding and removing rows
        // $('#add-financial-row-edit').on('click', function() {
        //     const index = $('#edit-financial-donation-rows-container .donation-row').length;
        //     $('#edit-financial-donation-rows-container').append(renderFinancialRow({}, index, []));
        // });

        $('#add-in-kind-row-edit').on('click', function() {
            const index = $('#edit-in-kind-donation-rows-container .donation-row').length;
            $('#edit-in-kind-donation-rows-container').append(renderInKindRow({}, index));
        });

        // $(document).on('click', '.remove-row-btn-edit', function() {
        //     $(this).closest('.donation-row').remove();
        // });
    });

    // function toggleEditDonationType(type) {
    //     if (type === 'Financial') {
    //         $('#edit-financial-donation-rows').removeClass('d-none');
    //         $('#edit-in-kind-donation-rows').addClass('d-none');
    //     } else {
    //         $('#edit-in-kind-donation-rows').removeClass('d-none');
    //         $('#edit-financial-donation-rows').addClass('d-none');
    //     }
    // }

    // function toggleEditDonationStatus(status) {
    //     console.log(status);
    //     const CollectingSection = document.getElementById('edit-collecting-section');

    //     if (status === 'collected') {
    //         CollectingSection.classList.remove('d-none');
    //     } else if (status === 'not_collected') {
    //         CollectingSection.classList.add('d-none');
    //     }
    // }

    function editDonation(id) {
        var form = $('#editDonationForm');
        form.trigger('reset');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        form.attr('action', `{{ route('donations.update', '') }}/${id}`);
        $('#editDonationModal').modal('show');

        $.get(`{{ url('donations') }}/${id}/edit`)
            .done(function(data) {

                // Populate basic fields
                $('#edit_donor_id').val(data.donor_id).trigger('change');
                $('#edit_date').val(data.date);
                $('#edit_donation_status').val(data.status).trigger('change');
                $('#edit_donation_type').val(data.donation_type).trigger('change');
                $('#edit_collecting_date').val(formatDate(data.collecting_donation?.collecting_date));
                $('#edit_financial_receipt_number').val(data.collecting_donation?.financial_receipt_number);
                $('#edit_in_kind_receipt_number').val(data.collecting_donation?.in_kind_receipt_number);
                $('#edit_employee_id').val(data.collecting_donation?.employee_id).trigger('change');
                $('#edit_notes').val(data.notes);
                $('#edit_collecting_time').val(data.collecting_time);

                // toggleEditDonationType(data.donation_type);
                // toggleEditDonationStatus(data.status);


                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }


                const financialContainer = $('#edit-financial-donation-rows-container');
                const inKindContainer = $('#edit-in-kind-donation-rows-container');
                financialContainer.empty();
                inKindContainer.empty();

                const financialSection = document.getElementById('edit-financial-donations-section');
                const inKindSection = document.getElementById('edit-in-kind-donations-section');


                if (data.donation_type === 'financial') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.add('d-none');

                    // Populate financial donations
                    data.donate_items
                        .filter(donation => donation.donation_type === 'financial')
                        .forEach((donation, index) => {
                            financialContainer.append(renderFinancialRow(donation, index, donationCategories));
                        });

                    // Check and toggle visibility of the add button
                    toggleAddRowButton();
                }

                if (data.donation_type === 'inKind') {
                    financialSection.classList.add('d-none');
                    inKindSection.classList.remove('d-none');

                    // Populate in-kind donations
                    data.donate_items
                        .filter(donation => donation.donation_type === 'inKind')
                        .forEach((donation, index) => {
                            inKindContainer.append(renderInKindRow(donation, index));
                        });
                }

                if(data.donation_type === 'both') {
                    financialSection.classList.remove('d-none');
                    inKindSection.classList.remove('d-none');

                    console.log(data.donate_items);
                    // Populate financial donations
                    data.donate_items
                        .filter(donation => donation.donation_type === 'financial')
                        .forEach((donation, index) => {
                            financialContainer.append(renderFinancialRow(donation, index, donationCategories));
                        });

                    // Populate in-kind donations
                    data.donate_items
                        .filter(donation => donation.donation_type === 'inKind')
                        .forEach((donation, index) => {
                            inKindContainer.append(renderInKindRow(donation, index));   
                        });

                }

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
            <div class="col-md-4">
                <label class="form-label">{{__('Donation Category')}}</label>
                <select class="form-control" name="donates[${index}][financial_donation_categories_id]">
                    ${categoryOptions}
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{__('Amount')}}</label>
                <input type="number" class="form-control" name="donates[${index}][financial_amount]" value="${donation.amount || ''}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <button type="button" class="btn btn-danger remove-row-btn-edit">{{__('Remove')}}</button>
            </div>
        </div>`;
    }

    function renderInKindRow(donation, index) {
        return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][inKind_donation_type]" value="inKind">
            <input type="hidden" name="donates[${index}][inKind_donation_id]" value="${donation.id || ''}">
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

    function toggleAddRowButton() {
        console.log($('#edit-financial-donation-rows-container .donation-row').length)
        if ($('#edit-financial-donation-rows-container .donation-row').length === 0) {
            $('#add-financial-row-edit').show(); // Show button if no rows
        } else {
            $('#add-financial-row-edit').hide(); // Hide button if there are rows
        }
    }

    function donationDetails(id) {
    $('#detailsDonationModal').modal('show');

    $.get(`{{ url('donations') }}/${id}/details`)
        .done(function(data) {
            // Construct the content to be displayed in the modal
            let modalContent = `
            <h5>{{__('Donor Information')}}</h5>
            <p><strong>{{__('Name')}}:</strong> ${data.donor.name}</p>
            <p><strong>{{__('Address')}}:</strong> ${data.donor.address}</p>
            <p><strong>{{__('Donor Type')}}:</strong> 
            ${data.donor.donor_type === 'normal' ? '{{__("Normal")}}' : '{{__("Monthly")}}'}
            </p>

            <h5>{{__('Donation Information')}}</h5>
            <p><strong>{{__('Donation Type')}}:</strong> ${data.donation_type}</p>
            <p><strong>{{__('Donation Date')}}:</strong> ${data.date}</p>
            <p><strong>{{__('Status')}}:</strong> ${data.status === 'collected' ? '{{__("Collected")}}' : '{{__("Not Collected")}}'}</p>`;

            // Financial Donations Table
            if (data.donation_type === 'financial' || data.donation_type === 'both') {
                modalContent += `
                <h5>{{__('Financial Donations')}}</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{__('Donation Category')}}</th>
                            <th>{{__('Amount')}}</th>
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
            <p><strong>{{__('Financial Receipt Number')}}:</strong> ${data.collecting_donation?.financial_receipt_number ?? 'N/A'}</p>
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
</script>
@endpush