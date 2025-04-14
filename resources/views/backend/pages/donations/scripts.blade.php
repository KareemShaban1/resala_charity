@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Declare donationTable as a global variable
    let donationTable;
    let monthlyDonationTable;
    let gatheredDonationTable;

    $(function() {
        // Initialize DataTable
        donationTable = $('#donations-table').DataTable({
            ajax: {
                url: "{{ route('donations.data') }}",
                data: function(d) {
                    // d.status = 'ongoing';
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    d.donation_category = $('#donation-category-filter').val();
                    d.status = $('#status-filter').val();
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
                        if (!data) return '<div>N/A</div>';
                        return data
                            .split(', ')
                            .map(phone => `<div>${phone}</div>`)
                            .join('');
                    }

                },
                {
                    data: 'donation_category',
                    name: 'donation_category'
                },
                {
                    data: 'donation_status',
                    name: 'donation_status'
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                    visible: false
                },
                {
                    data: 'user_department',
                    name: 'user_department',
                    visible: false
                },

                {
                    data: 'donateItems', // Add the 'donates' column
                    name: 'donateItems',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'collecting_line_number',
                    name: 'collecting_lines.number',
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Donation Data',
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
                    extend: 'colvis',
                    text: 'Columns',
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
            ],
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

        monthlyDonationTable = $('#monthly-donations-table').DataTable({
            ajax: {
                url: "{{ route('donations.data') }}",
                data: function(d) {
                    d.status = 'ongoing';
                    d.test = 'test';
                    d.donation_category = 'monthly';
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    // d.donation_category = $('#donation-category-filter').val(); 
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
                        if (!data) return '<div>N/A</div>';
                        return data
                            .split(', ')
                            .map(phone => `<div>${phone}</div>`)
                            .join('');
                    }

                },
                {
                    data: 'donation_category',
                    name: 'donation_category'
                },
                {
                    data: 'donation_status',
                    name: 'donation_status'
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
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
            ],
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

        gatheredDonationTable = $('#gathered-donations-table').DataTable({
            ajax: {
                url: "{{ route('donations.data') }}",
                data: function(d) {
                    d.status = 'ongoing';
                    d.donation_category = 'gathered';
                    d.date_filter = $('#date-filter').val();
                    d.start_date = $('#start-date').val();
                    d.end_date = $('#end-date').val();
                    // d.donation_category = $('#donation-category-filter').val(); 
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
                        if (!data) return '<div>N/A</div>';
                        return data
                            .split(', ')
                            .map(phone => `<div>${phone}</div>`)
                            .join('');
                    }

                },
                {
                    data: 'donation_category',
                    name: 'donation_category'
                },
                {
                    data: 'donation_status',
                    name: 'donation_status'
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
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000, 2000],
                [10, 25, 50, 100, 500, 1000, 2000]
            ],
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            },
            rowCallback: function(row, data) {
                if (data.donation_category === 'تبرع مجمع' && data.group_key) {
                    let color = generateColor(data.group_key);
                    $('td:eq(0)', row).css('background-color', color); // Apply color only to the first column (ID)
                    $('td:eq(0)', row).css('color', 'white'); // Apply color only to the first column (ID)

                }
            }
        });

        // Function to generate a consistent color based on the group_key
        function generateColor(groupKey) {
            let hash = 0;
            for (let i = 0; i < groupKey.length; i++) {
                hash = groupKey.charCodeAt(i) + ((hash << 5) - hash);
            }
            let color = '#';
            for (let i = 0; i < 3; i++) {
                color += ('00' + ((hash >> (i * 8)) & 0xFF).toString(16)).slice(-2);
            }
            return color;
        }

        // Date filter change
        $('#date-filter').on('change', function() {
            if ($(this).val() === 'range') {
                $('#custom-range, #end-date-container').show();
            } else {
                $('#custom-range, #end-date-container').hide();
                $('#start-date, #end-date').val('');
            }
            donationTable.ajax.reload();
            gatheredDonationTable.ajax.reload();
            monthlyDonationTable.ajax.reload();
        });

        // Start date and end date change
        $('#start-date, #end-date').on('change', function() {
            donationTable.ajax.reload();
        });

        // Donation category filter change
        $('#donation-category-filter').on('change', function() {
            donationTable.ajax.reload();
        });

        $('#status-filter').on('change', function() {
            donationTable.ajax.reload();
        });

        // Clear filters
        $('#clear-filters').on('click', function() {
            $('#date-filter').val('all');
            $('#start-date, #end-date').val('');
            $('#donation-category-filter').val('all');
            $('#custom-range, #end-date-container').hide();
            donationTable.ajax.reload();
        });



        let financialRowIndex = 0;
        let inKindRowIndex = 0;

        // Add row for Financial Donations
        // $(document).on('click', '.add-row-btn', function() {
        //     const container = $($(this).data('target'));
        //     if (container.attr('id') === 'financial-donation-rows-container' || container.attr('id') === 'edit-financial-donation-rows-container') {
        //         financialRowIndex++;
        //         console.log(container, financialRowIndex);
        //         container.append(`
        //     <div class="row donation-row">
        //         <input type="hidden" name="donates[${financialRowIndex}][financial_donation_type]" value="financial">

        //         <div class="col-md-3">
        //             <div class="mb-3">
        //                 <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
        //                 <select class="form-control donation-category" name="donates[${financialRowIndex}][financial_donation_categories_id]">
        //                     <option value="">{{__('Select Category')}}</option>
        //                     @foreach($donationCategories as $category)
        //                         <option value="{{ $category->id }}">{{ $category->name }}</option>
        //                     @endforeach
        //                 </select>
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>
        //         <div class="col-md-3">
        //             <div class="mb-3">
        //                 <label for="amount" class="form-label">{{__('Amount')}}</label>
        //                 <input type="number" class="form-control amount" name="donates[${financialRowIndex}][financial_amount]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>

        //          <div class="col-md-3">
        //             <div class="mb-3">
        //                 <label for="amount" class="form-label">{{__('Financial Receipt Number')}}</label>
        //                 <input type="text" class="form-control amount" name="donates[${financialRowIndex}][financial_receipt_number]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>

        //         <div class="col-md-3 d-flex align-items-center">
        //             <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
        //         </div>
        //     </div>
        // `);
        //     } else if (container.attr('id') === 'in-kind-donation-rows-container' || container.attr('id') === 'edit-in-kind-donation-rows-container') {
        //         inKindRowIndex++;
        //         container.append(`
        //     <div class="row donation-row">
        //         <input type="hidden" name="donates[${inKindRowIndex}][inKind_donation_type]" value="inKind">
        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="item_name" class="form-label">{{__('Item Name')}}</label>
        //                 <input type="text" class="form-control" name="donates[${inKindRowIndex}][in_kind_item_name]">
        //                 <div class="invalid-feedback"></div>
        //             </div>
        //         </div>
        //         <div class="col-md-4">
        //             <div class="mb-3">
        //                 <label for="quantity" class="form-label">{{__('Quantity')}}</label>
        //                 <input type="number" class="form-control" name="donates[${inKindRowIndex}][in_kind_quantity]">
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
                        <input type="number" step="0.01" class="form-control amount" name="donates[${newIndex}][financial_amount]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="financial_receipt_number" class="form-label">{{__('Financial Receipt Number')}}</label>
                        <input type="text" class="form-control" name="donates[${newIndex}][financial_receipt_number]">
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
                        <input type="number" step="0.01" class="form-control" name="donates[${newIndex}][in_kind_quantity]">
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
                        donationTable.ajax.reload();
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
                            title: '{{ __("validation.Validation Error") }}', // Ensure this is rendered as a string by Blade
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
                        query: params.term, // Search query
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

        $('#gathered_donation_donor_id').select2({
            dropdownParent: $('#addGatheredDonationModal'),
            placeholder: "{{__('Search By Name or Phone')}}",
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

        $('#add-in-kind-row-edit').on('click', function() {
            const index = $('#edit-in-kind-donation-rows-container .donation-row').length;
            $('#edit-in-kind-donation-rows-container').append(renderInKindRow({}, index));
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

    }


    function toggleEditDonationType() {
        const donationType = document.getElementById('edit_donation_type').value;
        const editFinancialContainer = document.getElementById('edit-financial-donations-section');
        const editInKindContainer = document.getElementById('edit-in-kind-donations-section');
        // const financialReceiptConatiner = document.getElementById('edit-financial-receipt-container');
        // const inKindReceiptConatiner = document.getElementById('edit-in-kind-receipt-container');

        if (donationType === 'financial') {
            editFinancialContainer.classList.remove('d-none');
            // editInKindContainer.classList.add('d-none');
            // financialReceiptConatiner.classList.remove('d-none');
            // inKindReceiptConatiner.classList.add('d-none');
        } else if (donationType === 'inKind') {
            editFinancialContainer.classList.add('d-none');
            // editInKindContainer.classList.remove('d-none');
            // financialReceiptConatiner.classList.add('d-none');
            // inKindReceiptConatiner.classList.remove('d-none');
        } else {
            editFinancialContainer.classList.remove('d-none');
            editInKindContainer.classList.remove('d-none');
            // financialReceiptConatiner.classList.remove('d-none');
            // inKindReceiptConatiner.classList.remove('d-none');
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




    function donationDetails(id) {
        $('#detailsDonationModal').modal('show');

        $.get(`{{ url('donations') }}/${id}/details`)
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
          

            <h4 class="text-danger">{{__('Donation Information')}}</h4>
             <div class="row">
                <div class="col-md-3">
                    <p><strong>{{__('Donation Type')}}:</strong>
                    ${data.donation_type === 'financial' ? '{{__("Financial")}}' : data.donation_type === 'inKind' ? '{{__("In-Kind")}}' : '{{__("Both")}}'}
                    </p>
                </div>
                <div class="col-md-3">
                    <p><strong>
                    ${data.donation_category === 'gathered' ? '{{__("Due Date")}}' : '{{__("Donation Date")}}'}:
                    </strong>
                     ${data.date}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>{{__('Status')}}:</strong> 
                
                    ${data.status === 'collected' ? '{{__("Collected")}}' : '{{__("Not Collected")}}'}</p>
                </div>
                </div>
                 <div class="row">
                  <div class="col-md-3">
                    <p><strong>
                    ${data.donation_category === 'gathered' ? '{{__("Collecting Date")}}' : '{{__("Reporting Date")}}'}:</strong> 
                    
                    ${data.donation_category === 'gathered' ? formatDate(data.collecting_donation.collecting_date) : formatDate(data.created_at)}</p>
                 </div>
                 <div class="col-md-3">
                    <p><strong>{{__('Reporting Way')}}:</strong> 
                    ${data.reporting_way === 'call' ? '{{__("Call")}}' 
                    : data.reporting_way === 'whatsapp_chat' ? '{{__("Whatsapp Chat")}}' 
                    : data.reporting_way === 'location' ? '{{__("Location")}}' 
                    : '{{__("Other")}}' }</p>
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
                    <p><strong>{{__('Collecting Time')}}:</strong> ${data.collecting_time ?? 'N/A'}</p>
                  </div>
                    <div class="col-md-6">
                    <p><strong>{{__('Notes')}}:</strong> ${data.notes ?? 'N/A'}</p>
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
                            <th>{{__('Financial Receipt Number')}}</th>
                             <th>{{__('Total Gathered Amount')}}</th>
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
                            <td>${item.financial_receipt_number ?? "{{ __('N/A') }}"}</td>
                            <td>${data.total_gathered_amount ?? "{{ __('N/A') }}"}</td>
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

                if (data.donation_category !== 'gathered') {
                    // Collecting Donation Info
                    modalContent += `
            <h4 class="text-success">{{__('Collecting Donation Information')}}</h4>
                <p>
                <strong>{{ __('Collecting Date') }}:</strong> 
                ${data.collecting_donation?.collecting_date ? formatDate(data.collecting_donation?.collecting_date) : "{{ __('N/A') }}"}
                </p>
                <p>
                <strong>{{ __('In Kind Receipt Number') }}:</strong> 
                ${data.collecting_donation?.in_kind_receipt_number ?? "{{ __('N/A') }}"}
                </p>
                `;
                }

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

        // Reset the form fields
        form.trigger('reset');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        // Reset dropdowns and reinitialize Select2 if used
        form.find('select').val(null).trigger('change');

        // Clear dynamically added donation rows
        $('#edit-financial-donation-rows-container').empty();
        $('#edit-in-kind-donation-rows-container').empty();

        // Reset indices tracking
        existingFinancialIndices = new Set();
        existingInKindIndices = new Set();

        // Set the form action
        form.attr('action', `{{ route('donations.update', '') }}/${id}`);

        // Show modal
        $('#editDonationModal').modal('show');

        // **Show Loading Spinner & Disable Form Inputs**
        $('#editDonationLoader').removeClass('d-none'); // Show loading spinner
        form.find('input, select, textarea, button').prop('disabled', true); // Disable inputs

        $.ajax({
            url: `{{ url('donations') }}/${id}/edit`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Ensures Laravel returns JSON instead of redirecting
            },
            success: function(data) {
                // Handle data population here

                // Populate basic fields
                $('#edit_donor_id').val(data.donor_id).trigger('change');
                $('#edit_date').val(data.date);
                $('#edit_donation_status').val(data.status).trigger('change');
                $('#edit_donation_type').val(data.donation_type).trigger('change');
                $('#edit_donation_category').val(data.donation_category).trigger('change');
                $('#edit_reporting_way').val(data.reporting_way).trigger('change');
                $('#edit_collecting_date').val(formatDate(data.collecting_donation?.collecting_date));
                $('#edit_in_kind_receipt_number').val(data.collecting_donation?.in_kind_receipt_number);
                $('#edit_employee_id').val(data.collecting_donation?.employee_id).trigger('change');
                $('#edit_notes').val(data.notes);
                $('#edit_collecting_time').val(data.collecting_time);
                $('#edit_collecting_way').val(data.collecting_donation?.collecting_way).trigger('change');

                // Populate financial donations
                const financialContainer = $('#edit-financial-donation-rows-container');
                data.donate_items
                    .filter(item => item.donation_type === 'financial')
                    .forEach((donationItem, index) => {
                        existingFinancialIndices.add(index);
                        financialContainer.append(renderFinancialRow(donationItem, index, donationCategories));
                    });

                // Populate in-kind donations
                const inKindContainer = $('#edit-in-kind-donation-rows-container');
                data.donate_items
                    .filter(item => item.donation_type === 'inKind')
                    .forEach((donationItem, index) => {
                        existingInKindIndices.add(index);
                        inKindContainer.append(renderInKindRow(donationItem, index));
                    });

                // Toggle sections based on donation type
                toggleEditDonationType();
                toggleEditDonationStatus();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('{{ __("Failed to load donation details. Please try again.") }}');
            },
            complete: function() {
                // **Hide Loading Spinner & Enable Form Inputs**
                $('#editDonationLoader').addClass('d-none'); // Hide loading spinner
                form.find('input, select, textarea, button').prop('disabled', false); // Enable inputs
            }
        });


    }



    function renderFinancialRow(donationItem, index, categories) {
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
                <input type="number" step="0.01" class="form-control" name="donates[${index}][financial_amount]" value="${donationItem.amount || ''}">
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
        return `
        <div class="row donation-row">
            <input type="hidden" name="donates[${index}][inKind_donation_type]" value="inKind">
            <input type="hidden" name="donates[${index}][inKind_donation_item_id]" value="${donationItem.id || ''}">
            <div class="col-md-3">
                <label class="form-label">{{__('Item Name')}}</label>
                <input type="text" class="form-control" name="donates[${index}][in_kind_item_name]" value="${donationItem.item_name || ''}">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{__('Quantity')}}</label>
                <input type="number" step="0.01" class="form-control" name="donates[${index}][in_kind_quantity]" value="${donationItem.amount || ''}">
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
        const donationId = row.find('input[name*="_donation_item_id"]').val(); // Extract donation ID

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
                    donationTable.ajax.reload();
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
                        title: '{{ __("validation.Validation Error") }}', // Ensure this is rendered as a string by Blade
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


















    $(document).ready(function() {
        // Listen for changes to the number_of_months input
        $('#number_of_months').on('input', function() {
            const numberOfMonths = parseInt($(this).val());
            if (!isNaN(numberOfMonths) && numberOfMonths > 0) {
                generateDonationRows(numberOfMonths);
            } else {
                // Clear the rows if the input is invalid
                $('#gathered-financial-donation-rows-container').empty();
            }
        });

        // Handle row removal
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('.donation-row').remove(); // Remove the row
        });

        // Clear rows when modal is closed
        $('#addGatheredDonationModal').on('hidden.bs.modal', function() {
            $('#gathered-financial-donation-rows-container').empty(); // Clear rows
            $('#number_of_months').val(''); // Reset the number of months input
        });
    });

    function getFirstDayOfNextMonth() {
        const today = new Date();
        const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 2); // First day of next month
        return nextMonth;
    }

    function generateDonationRows(numberOfMonths) {
        const container = $('#gathered-financial-donation-rows-container');
        container.empty(); // Clear existing rows

        const firstDayOfNextMonth = getFirstDayOfNextMonth(); // Get the first day of the next month

        for (let i = 0; i < numberOfMonths; i++) {
            const monthIndex = i + 1; // Start from 1 instead of 0
            const collectingDate = new Date(firstDayOfNextMonth);
            collectingDate.setMonth(firstDayOfNextMonth.getMonth() + i); // Increment by the number of months

            // Format the date as YYYY-MM-DD
            const formattedDate = collectingDate.toISOString().split('T')[0];

            const row = `
            <div class="row donation-row">
                <input type="hidden" name="donates[${i}][financial_donation_type]" value="financial">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">{{__('Donation Category')}}</label>
                        <select class="form-control donation-category" name="donates[${i}][financial_donation_categories_id]">
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
                        <label class="form-label">{{__('Amount')}}</label>
                        <input type="number" step="0.01" class="form-control amount" name="donates[${i}][financial_amount]">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">{{__('Date')}}</label>
                        <input type="date" class="form-control" name="donates[${i}][date]" value="${formattedDate}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <button type="button" class="btn btn-danger mt-2 remove-row-btn">{{__('Remove')}}</button>
                </div>
            </div>
        `;
            container.append(row); // Add the row to the container
        }
    }

    // Attach a submit event listener to the form
    $('#addGatheredDonationForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the form data
        const formData = new FormData(this);

        // Send an AJAX request
        $.ajax({
            url: '{{ route("donations.store-gathered-donation") }}', // Replace with your route
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addGatheredDonationModal').modal('hide'); // Close the modal
                // Optionally, reset the form
                $('#addGatheredDonationForm')[0].reset();
                donationTable.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = Object.values(errors).map(function(error) {
                        return error[0];
                    }).join('<br>');

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: errorMessages
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

            // Check for F1 key
            if (event.key === 'F1') {
                $('#addDonationModal').modal('show'); // Open the "Add Donor" modal
            }
        }
        if (event.key === 'F2') {
            // Check if the "Add Monthly Form" modal is open
            if ($('#addDonationModal').is(':visible')) {
                $('#addDonationForm').submit(); // Submit the "Add" form
            }
            // Check if the "Edit Monthly Form" modal is open
            else if ($('#editDonationModal').is(':visible')) {
                $('#editDonationForm').submit(); // Submit the "Edit" form
            }

        }
    });

    function addActivity(donorId) {
        console.log(donorId);
        $('#addActivityModal').modal('show');
        $('#add_activity_donor_id').val(donorId);


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
                    donationTable.ajax.reload();
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


    $('#call_type_id').on('change', function() {
        const callTypeSelect = document.getElementById('call_type_id');
        const statusContainer = document.getElementById('status-container');

        // Ensure there are at least two options
        if (callTypeSelect.options.length > 1) {
            const secondOptionValue = callTypeSelect.options[1].value; // Get the second option's value

            // Debugging: Check selected value and second option value
            console.log(callTypeSelect.value, secondOptionValue);

            // Ensure the status container visibility changes based on the selected value
            if (callTypeSelect.value === secondOptionValue) {
                statusContainer.style.display = 'block';
            } else {
                statusContainer.style.display = 'none';
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
</script>
@endpush