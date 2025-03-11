@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let table;

    $(document).ready(function() {
        // Initialize Select2 for main form
        initializeSelect2();

        // Initialize DataTable
        initializeDataTable();

        // Bind events
        bindEvents();

    
    });

    function initializeSelect2() {
        // Main form selects
        $('#governorate_id').select2({
            dropdownParent: $('#addDonorModal'),
            placeholder: '{{__('Select Governorate')}}',
            allowClear: true,
            width: '100%'
        });

        $('#city_id').select2({
            dropdownParent: $('#addDonorModal'),
            placeholder: '{{__('Select City')}}',
            allowClear: true,
            width: '100%'
        });

        $('#area_id').select2({
            dropdownParent: $('#addDonorModal'),
            placeholder: '{{__('Select Area')}}',
            allowClear: true,
            width: '100%'
        });


        $('#assign_donor_id').select2({
            dropdownParent: $('#assignDonorModal'),
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

    }

    function initializeModalSelect2() {
        // Modal form selects
        $('#edit_governorate_id').select2({
            dropdownParent: $('#editDonorModal'),
            placeholder: '{{__('Select Governorate')}}',
            allowClear: true,
            width: '100%'
        });

        $('#edit_city_id').select2({
            dropdownParent: $('#editDonorModal'),
            placeholder: '{{__('Select City')}}',
            allowClear: true,
            width: '100%'
        });

        $('#edit_area_id').select2({
            dropdownParent: $('#editDonorModal'),
            placeholder: '{{__('Select Area')}}',
            allowClear: true,
            width: '100%'
        });
    }

    function bindEvents() {
        // Main form events
        $('#governorate_id').on('change', function() {
            var governorateId = $(this).val();
            if (governorateId) {
                loadCities(governorateId, $('#city_id'));
                $('#area_id').empty().trigger('change');
            } else {
                $('#city_id, #area_id').empty().trigger('change');
            }
        });

        $('#city_id').on('change', function() {
            var cityId = $(this).val();
            if (cityId) {
                loadAreas(cityId, $('#area_id'));
            } else {
                $('#area_id').empty().trigger('change');
            }
        });



        $('#edit_governorate_id').on('change', function() {
            var governorateId = $(this).val();
            console.log(governorateId);
            if (governorateId) {
                loadCities(governorateId, $('#edit_city_id'));
                $('#edit_area_id').empty().trigger('change');
            } else {
                $('#edit_city_id, #edit_area_id').empty().trigger('change');
            }
        });

        $('#edit_city_id').on('change', function() {
            var cityId = $(this).val();
            if (cityId) {
                loadAreas(cityId, $('#edit_area_id'));
            } else {
                $('#edit_area_id').empty().trigger('change');
            }
        });

        // Modal events
        $('#editDonorModal').on({
            'show.bs.modal': function() {
                // Clear previous states
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');
                
            },
            'shown.bs.modal': function() {
                // Initialize Select2 after modal is shown
                initializeModalSelect2();
            },
            'hide.bs.modal': function() {
                // Destroy Select2 instances
                $('#edit_governorate_id, #edit_city_id, #edit_area_id').select2('destroy');

                // Reset form
                $('#editDonorForm').trigger('reset');
                $('#edit-phone-container').empty();
                addPhoneField($('#edit-phone-container'));
            }
        });
    }

    function editDonor(id) {
        // Reset form state
        var form = $('#editDonorForm');
        form.trigger('reset');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        // Set form action
        form.attr('action', `{{ route('donors.update', '') }}/${id}`);

        // Show modal
        $('#editDonorModal').modal('show');

        // Get and populate data
        $.get(`{{ url('donors') }}/${id}/edit`, function(data) {
            $('#edit_name').val(data.name);
            $('#edit_address').val(data.address);
            $('#edit_street').val(data.street);
            $('#edit_donor_type').val(data.donor_type);
            $('#edit_donor_category').val(data.donor_category);
            $('#edit_department_id').val(data.department_id).trigger('change');
            $('#edit_notes').val(data.notes);
            $('#edit_monthly_donation_day').val(data.monthly_donation_day);
            $('#edit_active')
                .val(data.active ? "1" : "0") // Convert true/false to "1"/"0"
                .change();

            // Set governorate and trigger cascading dropdowns
            if (data.governorate_id) {
                $('#edit_governorate_id').val(data.governorate_id).trigger('change');

                // Load cities and ensure the correct city is selected before loading areas
            loadCities(data.governorate_id, $('#edit_city_id'), data.city_id)
                .then(() => {
                    if (data.city_id) {
                        return loadAreas(data.city_id, $('#edit_area_id'), data.area_id);
                    }
                });


            }

            // Handle phones
            const phoneContainer = $('#edit-phone-container');
            phoneContainer.empty();
            if (data.phones && data.phones.length > 0) {
                data.phones.forEach(function(phone) {
                    addPhoneField(phoneContainer, phone.phone_number, phone.phone_type, phone.id);
                });
            } else {
                addPhoneField(phoneContainer);
            }
        });
    }


    function donorDetails(id){

        $('#donorDetailsModal').modal('show');

        $.get(`{{ url('donors') }}/${id}`)
        .done(function(data) {

            let modalContent = `
            <h4 class="text-primary">{{__('Donor Information')}}</h4>
            <div class="row mb-3">
                <div class="col-md-3">
                            <p><strong>{{__('Name')}}:</strong> ${data.name}</p>
                </div>
                  <div class="row">
                    <div class="col-md-4">
                          <p><strong>{{__('Governorate')}}:</strong> ${data.governorate?.name ?? ''}</p>
                    </div>
                     <div class="col-md-4">
                          <p><strong>{{__('City')}}:</strong> ${data.city?.name ?? ''}</p>
                    </div>
                     <div class="col-md-4">
                          <p><strong>{{__('Area')}}:</strong> ${data.area?.name ?? ''}</p>
                    </div>
                </div>
                <div class="col-md-12">
                            <p><strong>{{__('Address')}}:</strong> ${data.address ?? ''}</p>
                </div>
                  <div class="col-md-12">
                            <p><strong>{{__('Street')}}:</strong> ${data.street ?? ''}</p>
                </div>

              
                <div class="col-md-4">
                            <p><strong>{{__('Donor Type')}}:</strong> 
                            ${data.donor_type === 'normal' ? "{{ __('Normal')}}" : 
                            "{{__('Monthly') }}" ?? ''}</p>
                </div>

                 <div class="col-md-4">
                            <p><strong>{{__('Monthly Donation Day')}}:</strong> 
                            ${data.donor_type === 'monthly' ? data.monthly_donation_day : ''}</p>
                </div>

                <div class="col-md-4">
                            <p><strong>{{__('Donor Category')}}:</strong> ${
                            
                            data.donor_category === 'normal' ? " {{__('Normal') }}" :
                            data.donor_category === 'random' ? " {{__('Random') }}"  : "{{__('Special')}}" ?? ''}</p>
                </div>

                 <div class="col-md-4">
                            <p><strong>{{__('Status')}}:</strong> 
                            ${data.active === true ? "{{__('Active')}}" : "{{__('Not Active')}}"}</p>
                </div>

                 <div class="col-md-4">
                          <p><strong>{{__('Department')}}:</strong> ${data.department?.name ?? ''}</p>
                    </div>
                

                  <div class="col-md-12">
                            <p><strong>{{__('Notes')}}:</strong> ${data.notes ?? ''}</p>
                </div>


                 <h4 class="text-primary">{{__('Donor Phones')}}</h4>
                
                `;

                data.phones
                        .forEach(phone => {
                            modalContent += `
                            <div class="col-md-12"> ${phone.phone_number} --- (${phone.phone_type}) </div>
                            `;
                        })

                        modalContent += `
                          <h4 class="text-primary mt-3">{{__('Activities')}}</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{__('Activity Type')}}</th>
                                    <th>{{__('Call Type')}}</th>
                                    <th>{{__('Date Time')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Response')}}</th>
                                    <th>{{__('Notes')}}</th>
                                    <th>{{__('Created By')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.activities
                                .map(activity => `
                                    <tr>
                                        <td>${activity.activity_type}</td>
                                        <td>${activity.call_type?.name}</td>
                                        <td>${activity.date_time}</td>
                                        <td>
                                           ${activity.activity_status.name ?? '' }
                                        </td>
                                        <td>${activity.response ?? ''}</td>
                                        <td>${activity.notes ?? ''}</td>
                                        <td>${activity.created_by?.name}</td>
                                    </tr>
                                `)
                                .join('')}
                                
                            </tbody>
                        
                        </table>
                        
                        `;

              // Add the constructed content to the modal body
              $('#donorDetailsModal .modal-body').html(modalContent);    
        });

    }


    // Add phone field function
    function addPhoneField(container, phone = '', type = 'mobile', id = '') {
        const index = container.children().length;
        const phoneHtml = `
            <div class="input-group mb-2">
                <input type="hidden" name="phones[${index}][id]" value="${id}">
                <input type="text" name="phones[${index}][number]" class="form-control" placeholder="Enter phone number" value="${phone}">
                <select name="phones[${index}][type]" class="form-select" style="max-width: 150px;">
                    <option value="mobile" ${type === 'mobile' ? 'selected' : ''}>{{__('Mobile')}}</option>
                    <option value="home" ${type === 'home' ? 'selected' : ''}>{{__('Home')}}</option>
                    <option value="work" ${type === 'work' ? 'selected' : ''}>{{__('Work')}}</option>
                    <option value="other" ${type === 'other' ? 'selected' : ''}>{{__('Other')}}</option>
                </select>
                ${index === 0 ? 
                    `<button type="button" class="btn btn-success add-phone"><i class="mdi mdi-plus"></i></button>` :
                    `<button type="button" class="btn btn-danger remove-phone"><i class="mdi mdi-minus"></i></button>`
                }
            </div>
        `;
        container.append(phoneHtml);
    }

    // Handle add phone button click
    $(document).on('click', '.add-phone', function() {
        const container = $(this).closest('.mb-3').find('#phone-container, #edit-phone-container');
        addPhoneField(container);
    });

    // Handle remove phone button click
    // $(document).on('click', '.remove-phone', function() {
    //     $(this).closest('.input-group').remove();
    // });

    let donorsTable;
    let randomDonorsTable;


    // Initialize DataTable
    function initializeDataTable() {
        donorsTable = $('#donors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('donors.data') }}",
            data: function(d) {
                d.date_filter = $('#date-filter').val();
                d.start_date = $('#start-date').val();
                d.end_date = $('#end-date').val();
                d.category = 'normal';

            }
        },
        autoWidth:false,
        columnDefs: [
            {width: "20px"  , targets: 0}, // ID column
            { width: "10p%" ,targets: 1,  }, // Name column
            {  width: "50px",targets: 2 }, // Donor type column
            ],
            columns: [{
                    data: 'id',
                    name: 'donors.id' // Explicitly specify the table name
                },
                {
                    data: 'name',
                    name: 'donors.name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'donor_type',
                    name: 'donors.donor_type',
                    render: function(data, type, row) {
                        return data === 'monthly' ? 'شهرى' : 'عادى';
                    },
                    orderable: false,
                },
                {
                    data: 'donor_category',
                    name: 'donors.donor_category',
                    render: function(data, type, row) {
                        if(data === 'special'){
                            return 'مميز';
                        }else if (data === 'normal'){
                            return 'عادى';
                        }else {
                            return 'عشوائى';
                        }
                    },
                    orderable: false,
                },
                {
                    data: 'city',
                    name: 'city.name',
                    orderable: false,

                },
                {
                    data: 'area',
                    name: 'area.name',
                    orderable: false,

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
                    data: 'active',
                    name: 'donors.active',
                    orderable: false,
                    searchable: true
                },
                // {
                //     data: 'has_activities',
                //     name: 'has_activities',
                //     orderable: false,
                //     searchable: true
                // },
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
            search: {
                regex: true
            },
            buttons: [
    {
        extend: 'excel',
        text: 'Excel',
        title: 'Donors Data',
        exportOptions: {
            columns: ':visible', // Ensures all visible columns are exported
            header: true,
            format: {
                header: function (data, columnIdx) {
                    // Select the first row of <thead> and exclude the second row (filters)
                    if ($('#donors-table thead tr:first-child th').eq(columnIdx).length) {
                        return $('#donors-table thead tr:first-child th').eq(columnIdx).text();
                    }
                    return '';
                }
            }
        }
    },
    {
        extend: 'print',
        text: 'Print',
        exportOptions: {
            columns: ':visible',
            header: true,
            format: {
                header: function (data, columnIdx) {
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
        text: 'Copy',
        exportOptions: {
            columns: ':visible',
            header: true,
            format: {
                header: function (data, columnIdx) {
                    if ($('#donors-table thead tr:first-child th').eq(columnIdx).length) {
                        return $('#donors-table thead tr:first-child th').eq(columnIdx).text();
                    }
                    return '';
                }
            }
        }
    }
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



        randomDonorsTable = $('#random-donors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('donors.data') }}",
            data: function(d) {
                d.date_filter = $('#date-filter').val();
                d.start_date = $('#start-date').val();
                d.end_date = $('#end-date').val();
                d.category = 'random';
            }
        },
        autoWidth:false,
        columnDefs: [
            {width: "20px"  , targets: 0}, // ID column
            { width: "10p%" ,targets: 1,  }, // Name column
            {  width: "50px",targets: 2 }, // Donor type column
            ],
            columns: [{
                    data: 'id',
                    name: 'donors.id' // Explicitly specify the table name
                },
                {
                    data: 'name',
                    name: 'donors.name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'donor_type',
                    name: 'donors.donor_type',
                    render: function(data, type, row) {
                        return data === 'monthly' ? 'شهرى' : 'عادى';
                    },
                    orderable: false,
                },
                {
                    data: 'donor_category',
                    name: 'donors.donor_category',
                    render: function(data, type, row) {
                        if(data === 'special'){
                            return 'مميز';
                        }else if (data === 'normal'){
                            return 'عادى';
                        }else {
                            return 'عشوائى';
                        }
                    },
                    orderable: false,
                },
                {
                    data: 'city',
                    name: 'city.name',
                    orderable: false,

                },
                {
                    data: 'area',
                    name: 'area.name',
                    orderable: false,

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
                    data: 'active',
                    name: 'donors.active',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'has_activities',
                    name: 'has_activities',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'last_activity_status',
                    name: 'last_activity_status',
                    orderable: false,
                    searchable: true
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
            search: {
                regex: true
            },
            buttons: [
    {
        extend: 'excel',
        text: 'Excel',
        title: 'Donors Data',
        exportOptions: {
            columns: ':visible', // Ensures all visible columns are exported
            header: true,
            format: {
                header: function (data, columnIdx) {
                    // Select the first row of <thead> and exclude the second row (filters)
                    if ($('#random-donors-table thead tr:first-child th').eq(columnIdx).length) {
                        return $('#random-donors-table thead tr:first-child th').eq(columnIdx).text();
                    }
                    return '';
                }
            }
        }
    },
    {
        extend: 'print',
        text: 'Print',
        exportOptions: {
            columns: ':visible',
            header: true,
            format: {
                header: function (data, columnIdx) {
                    if ($('#random-donors-table thead tr:first-child th').eq(columnIdx).length) {
                        return $('#random-donors-table thead tr:first-child th').eq(columnIdx).text();
                    }
                    return '';
                }
            }
        }
    },
    {
        extend: 'copy',
        text: 'Copy',
        exportOptions: {
            columns: ':visible',
            header: true,
            format: {
                header: function (data, columnIdx) {
                    if ($('#random-donors-table thead tr:first-child th').eq(columnIdx).length) {
                        return $('#random-donors-table thead tr:first-child th').eq(columnIdx).text();
                    }
                    return '';
                }
            }
        }
    }
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
    }

        
        function getColorForParent(parentId) {
                // Use a hash function to generate a unique color
                const colors = [
                    '#f9f9f9', '#e6f7ff', '#fff7e6', '#e6ffe6', '#ffe6e6', 
                    '#e6e6ff', '#f0e6ff', '#ffe6f0', '#e6fff0', '#fff0e6'
                ];
                const index = parentId % colors.length; // Ensure the index is within the array bounds
                return colors[index];
            }



    // Trigger DataTable reload on input change
    $('#phone-search').on('keyup change', function() {
        donorsTable.draw();
    });

       // Date filter change
       $('#date-filter').on('change', function () {
        if ($(this).val() === 'range') {
            $('#custom-range, #end-date-container').show();
        } else {
            $('#custom-range, #end-date-container').hide();
            $('#start-date, #end-date').val('');
        }
        donorsTable.ajax.reload();
    });


    // Start date and end date change
    $('#start-date, #end-date').on('change', function () {
        donorsTable.ajax.reload();
    });


    $('#clear-filters').on('click', function() {
        $('#date-filter').val('all').trigger('change');
        $('#start-date, #end-date').val('');
        donorsTable.ajax.reload();
    });



    function loadCities(governorateId, targetSelect, selectedCityId = null) {
    targetSelect.prop('disabled', true);
    
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "{{ route('cities.by-governorate') }}",
            type: 'GET',
            data: { governorate_id: governorateId },
            success: function(response) {
                targetSelect.empty().append('<option value="">Select City</option>');

                if (response.success && Array.isArray(response.data)) {
                    response.data.forEach(city => {
                        targetSelect.append(`<option value="${city.id}" ${city.id == selectedCityId ? 'selected' : ''}>${city.name}</option>`);
                    });
                }
                
                targetSelect.prop('disabled', false);

                if (selectedCityId) {
                    targetSelect.val(selectedCityId).trigger('change');
                }

                resolve(); // Ensure it completes before proceeding
            },
            error: function(xhr) {
                console.error('Error loading cities:', xhr);
                targetSelect.prop('disabled', false);
                reject();
            }
        });
    });
}

function loadAreas(cityId, targetSelect, selectedAreaId = null) {
    targetSelect.prop('disabled', true);
    
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "{{ route('areas.by-city') }}",
            type: 'GET',
            data: { city_id: cityId },
            success: function(response) {
                targetSelect.empty().append('<option value="">Select Area</option>');

                if (response.success && Array.isArray(response.data)) {
                    response.data.forEach(area => {
                        targetSelect.append(`<option value="${area.id}" ${area.id == selectedAreaId ? 'selected' : ''}>${area.name}</option>`);
                    });
                }
                
                targetSelect.prop('disabled', false);

                if (selectedAreaId) {
                    targetSelect.val(selectedAreaId).trigger('change');
                }

                resolve();
            },
            error: function(xhr) {
                console.error('Error loading areas:', xhr);
                targetSelect.prop('disabled', false);
                reject();
            }
        });
    });
}


    // Add Donor Form Submit
    $('#addDonorForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addDonorModal').modal('hide');
                    form[0].reset();
                    $('.select2').val('').trigger('change');
                    donorsTable.ajax.reload();
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

    // Edit Donor Form Submit
    $('#editDonorForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editDonorModal').modal('hide');
                    form[0].reset();
                    $('.select2').val('').trigger('change');
                    donorsTable.ajax.reload();
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

    // Clear form validation on modal hide
    $('.modal').on('hidden.bs.modal', function() {
        var form = $(this).find('form');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
    });

    $('#importDonorForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        let formData = new FormData(this);

        // Clear previous messages
        $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

        // AJAX request
        $.ajax({
            url: "{{ route('donors.import') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#importDonorForm button[type="submit"]').prop('disabled', true);
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
                        $('#importDonorModal').modal('hide');
                        $('#importDonorForm')[0].reset();
                    }, 2000);

                    donorsTable.ajax.reload();
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
                $('#importDonorForm button[type="submit"]').prop('disabled', false);
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function deleteDonor(donorId) {
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
                    url: '/donors/' + donorId, // Adjust the URL to your route
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Donor deleted successfully!',
                        });
                        donorsTable.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the donor.',
                        });
                    }
                });
            }
        });
    }


    function assignDonor(donorId) {
    $('#assignDonorModal').modal('show');
    $('#parent_donor_id').val(donorId);
    
    const payload = {
        parent_donor_id: donorId,
        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
    };

    // Perform the AJAX request to fetch non-assigned children
    $.ajax({
        url: '/donors-not-assigned',  // New endpoint for non-assigned children
        method: 'POST',
        data: payload,
        success: function(data) {
            // Populate the donor select dropdown with non-assigned children
            const donorSelect = $('#assign_donor_id');
            donorSelect.html('<option value="">{{__('Select Donor')}}</option>'); // Reset the select options
            data.forEach(donor => {
                donorSelect.append(`<option value="${donor.id}">${donor.name}</option>`);
            });
        },
        error: function(xhr) {
            console.error("Error fetching non-assigned children donors:", xhr.responseText);
        }
    });

    $.ajax({
        url: '/donors-children',
        method: 'POST',
        data: payload,
        success: function(data) {
            // Populate the table
            const tableBody = $('#childrenDonorTableBody');
            tableBody.empty(); // Clear previous data
            data.forEach(donor => {
                tableBody.append(`
                    <tr>
                        <td>${donor.id}</td>
                        <td>${donor.name}</td>
                        <td>${donor.address}</td>
                        <td>
                            <button type="button" class="btn btn-primary" 
                            onclick="reAssignDonor(${donor.id}, ${donor.parent_id})">{{__('Re-Assign')}}</button>
                        </td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error("Error fetching children donors:", xhr.responseText);
        }
    });
}

function reAssignDonor(donorId , parentDonorId) {
    console.log(donorId , parentDonorId);
    const payload = {
        donor_id: donorId,
        parent_donor_id: parentDonorId,
        _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
    };
    $.ajax({
        url: '/donors-re-assign',  // New endpoint for non-assigned children
        method: 'POST',
        data: payload,
        success: function(response) {
            if (response.success) {
                    $('#assignDonorModal').modal('hide');
                    donorsTable.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                }
        },
        error: function(xhr) {
            console.error("Error fetching non-assigned children donors:", xhr.responseText);
        }
    });

}

function addActivity(donorId) {
    
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
                    donorsTable.ajax.reload();
                    randomDonorsTable.ajax.reload();
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

    $('#assignDonorForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#assignDonorModal').modal('hide');
                    form[0].reset();
                    $('.select2').val('').trigger('change');
                    donorsTable.ajax.reload();
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


    $(document).on('click', '.remove-phone', function() {
        const row = $(this).closest('.input-group'); // Get the correct row
        const phoneId = row.find('input[name*="[id]"]').val(); // Extract phone ID
    console.log(phoneId)

    if (!phoneId) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Phone ID not found.'
        });
        return;
    }

    Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('You wont be able to revert this!') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText:"{{ __('Cancel') }}",
        confirmButtonText: "{{ __('Yes, delete it!') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('donors/delete-donor-phone') }}/${phoneId}`, // Endpoint for deletion
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        response.message || 'Phone number removed successfully.',
                        'success'
                    );
                    row.remove(); // Remove the row from the DOM
                    $(this).closest('.input-group').remove();
                },
                error: function(error) {
                    console.error("Error deleting phone:", error);
                    Swal.fire(
                        'Error!',
                        'Failed to delete the phone number. Please try again.',
                        'error'
                    );
                }
            });
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
            $('#addDonorModal').modal('show'); // Open the "Add Donor" modal
        }

    }
    if (event.key === 'F2') {
            // Check if the "Add Monthly Form" modal is open
            if ($('#addDonorModal').is(':visible')) {
                $('#addDonorForm').submit(); // Submit the "Add" form
            }
            // Check if the "Edit Monthly Form" modal is open
            else if ($('#editDonorModal').is(':visible')) {
                $('#editDonorForm').submit(); // Submit the "Edit" form
            }

        }
});




</script>
@endpush