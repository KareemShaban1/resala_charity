@extends('backend.layouts.master')



@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addDonorModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Donor')}}
                    </button>
                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importDonorModal">
                        <i class="mdi mdi-upload"></i> {{__('Import Donors')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Donors')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
            
                <div class="card-body">
                
                    <table id="donors-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Donor Type')}}</th>
                                <th>{{__('City')}}</th>
                                <th>{{__('Area')}}</th>
                                <th>{{__('Phones')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Donor Modal -->
@include('backend.pages.donors.add_modal')

<!-- Edit Donor Modal -->
@include('backend.pages.donors.edit_modal')

<!-- Import Donor Modal -->
@include('backend.pages.donors.import_modal')

@include('backend.pages.donors.assign_donors_modal')

@include('backend.pages.donors.add_activity_modal')

@endsection

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
            placeholder: '{{__('Select Donor')}}',
            allowClear: true,
            width: '100%'
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

        // Modal form events
        // $('#edit_governorate_id').on('change', function() {
        //     var governorateId = $(this).val();
        //     if (governorateId) {
        //         loadCities(governorateId, $('#edit_city_id'));
        //         $('#edit_area_id').empty().trigger('change');
        //     } else {
        //         $('#edit_city_id, #edit_area_id').empty().trigger('change');
        //     }
        // });

        // $('#edit_city_id').on('change', function() {
        //     var cityId = $(this).val();
        //     if (cityId) {
        //         loadAreas(cityId, $('#edit_area_id'));
        //     } else {
        //         $('#edit_area_id').empty().trigger('change');
        //     }
        // });

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
            $('#edit_monthly_donation_day').val(data.monthly_donation_day);
            $('#edit_active')
                .val(data.active ? "1" : "0") // Convert true/false to "1"/"0"
                .change();

            // Set governorate and trigger cascading dropdowns
            if (data.governorate_id) {
                $('#edit_governorate_id').val(data.governorate_id).trigger('change');

                loadCities(data.governorate_id, $('#edit_city_id'), data.city_id);

                if (data.city_id) {
                    loadAreas(data.city_id, $('#edit_area_id'), data.area_id);
                }


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
    $(document).on('click', '.remove-phone', function() {
        $(this).closest('.input-group').remove();
    });

    // Initialize DataTable
    function initializeDataTable() {
        table = $('#donors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('donors.data') }}",
            data: function(d) {
                d.phone = $('#phone-search').val(); // Include the phone search term
            }
        },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'donor_name',
                    name: 'donor_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'donor_type',
                    name: 'donor_type',
                    render: function(data, type, row) {
                        return data === 'monthly' ? 'شهرى' : 'عادى';
                    }
                },
                // {
                //     data: 'address',
                //     name: 'address'
                // },
                // {
                //     data: 'governorate',
                //     name: 'governorate.name'
                // },
                {
                    data: 'city',
                    name: 'city.name'
                },
                {
                    data: 'area',
                    name: 'area.name'
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
                    name: 'active',
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
            search: {
                regex: true
            },
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Donors Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5,6]
                    }
                },
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Donors Data', 
                //     exportOptions: {
                //         columns: [0, 1, 2, 3]
                //     }
                // },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5,6]
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
    }

    // Trigger DataTable reload on input change
    $('#phone-search').on('keyup change', function() {
        table.draw();
    });

    // Load Cities based on Governorate
    function loadCities(governorateId, targetSelect, selectedCityId = null) {
        targetSelect.prop('disabled', true);
        $.ajax({
            url: "{{ route('cities.by-governorate') }}",
            type: 'GET',
            data: {
                governorate_id: governorateId
            },
            success: function(response) {
                if (response.success) {
                    targetSelect.empty().append('<option value="">Select City</option>');
                    if (Array.isArray(response.data)) {
                        response.data.forEach(function(city) {
                            targetSelect.append(`<option value="${city.id}" ${city.id == selectedCityId ? 'selected' : ''}>${city.name}</option>`);
                        });
                    }
                    targetSelect.prop('disabled', false).trigger('change');
                }
            },
            error: function(xhr) {
                console.error('Error loading cities:', xhr);
                targetSelect.prop('disabled', false);
            }
        });
    }

    // Load Areas based on City
    function loadAreas(cityId, targetSelect, selectedAreaId = null) {
        targetSelect.prop('disabled', true);
        $.ajax({
            url: "{{ route('areas.by-city') }}",
            type: 'GET',
            data: {
                city_id: cityId
            },
            success: function(response) {
                if (response.success) {
                    targetSelect.empty().append('<option value="">Select Area</option>');
                    if (Array.isArray(response.data)) {
                        response.data.forEach(function(area) {
                            targetSelect.append(`<option value="${area.id}" ${area.id == selectedAreaId ? 'selected' : ''}>${area.name}</option>`);
                        });
                    }
                    targetSelect.prop('disabled', false).trigger('change');
                }
            },
            error: function(xhr) {
                console.error('Error loading areas:', xhr);
                targetSelect.prop('disabled', false);
            }
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

                    table.ajax.reload();
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
                        table.ajax.reload();
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
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error("Error fetching children donors:", xhr.responseText);
        }
    });
}

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

</script>
@endpush