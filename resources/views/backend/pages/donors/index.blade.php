@extends('backend.layouts.master')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

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
                <div class="col-md-4 mb-4">
            <input id="phone-search" type="text" class="form-control" placeholder="{{__('Search by Phone')}}">
        </div>
                    <table id="donors-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Name')}}</th>
                                <!-- <th>{{__('Address')}}</th> -->
                                <th>{{__('Donor Type')}}</th>
                                <th>{{__('Governorate')}}</th>
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
<x-modal id="addDonorModal" title="{{__('Add Donor')}}" size="lg">
    <form id="addDonorForm" method="POST" action="{{ route('donors.store') }}">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Name')}}</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="governorate_id" class="form-label">{{__('Governorate')}}</label>
                        <select class="form-control select2" id="governorate_id" name="governorate_id" required>
                            <option value="">{{__('Select Governorate')}}</option>
                            @foreach(\App\Models\Governorate::all() as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="city_id" class="form-label">{{__('City')}}</label>
                        <select class="form-control select2" id="city_id" name="city_id" required>
                            <option value="">{{__('Select City')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">

                    <div class="mb-3">
                        <label for="area_id" class="form-label">{{__('Area')}}</label>
                        <select class="form-control select2" id="area_id" name="area_id">
                            <option value="">{{__('Select Area')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="mb-3">
                        <label for="street" class="form-label">{{__('Street')}}</label>
                        <input type="text" class="form-control" id="street" name="street" >
                        <div class="invalid-feedback"></div>
                    </div>

                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">{{__('Address')}}</label>
                <input type="text" class="form-control" id="address" name="address">
                <div class="invalid-feedback"></div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{__('Phone Numbers')}}</label>
                        <div id="phone-container">
                            <div class="input-group mb-2">
                                <input type="text" name="phones[0][number]" class="form-control" placeholder="{{__('Enter phone number')}}">
                                <select name="phones[0][type]" class="form-select" style="max-width: 150px;">
                                    <option value="mobile">{{__('Mobile')}}</option>
                                    <option value="home">{{__('Home')}}</option>
                                    <option value="work">{{__('Work')}}</option>
                                    <option value="other">{{__('Other')}}</option>
                                </select>
                                <button type="button" class="btn btn-success add-phone"><i class="mdi mdi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="donor_type" class="form-label">{{__('Donor Type')}}</label>
                        <select class="form-select" id="donor_type" name="donor_type">
                            <option value="normal">{{__('Normal')}}</option>
                            <option value="monthly">{{__('Monthly')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="active" class="form-label">{{__('Status')}}</label>
                        <select class="form-select" id="active" name="active">
                            <option value="1">{{__('Active')}}</option>
                            <option value="0">{{__('Inactive')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="monthly_donation_day" class="form-label">{{__('Monthly Donation Day')}}</label>
                        <input type="number" class="form-control" name="monthly_donation_day">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>

<!-- Edit Donor Modal -->
<x-modal id="editDonorModal" title="{{__('Edit Donor')}}" size="lg">
    <form id="editDonorForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_governorate_id" class="form-label">{{__('Governorate')}}</label>
                        <select class="form-control select2" id="edit_governorate_id" name="governorate_id" required>
                            <option value="">{{__('Select Governorate')}}</option>
                            @foreach(\App\Models\Governorate::all() as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_city_id" class="form-label">{{__('City')}}</label>
                        <select class="form-control select2" id="edit_city_id" name="city_id" required>
                            <option value="">{{__('Select City')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_area_id" class="form-label">{{__('Area')}}</label>
                        <select class="form-control select2" id="edit_area_id" name="area_id">
                            <option value="">{{__('Select Area')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_street" class="form-label">{{__('Street')}}</label>
                        <input type="text" class="form-control" id="edit_street" name="street">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="edit_address" class="form-label">{{__('Address')}}</label>
                <input type="text" class="form-control" id="edit_address" name="address">
                <div class="invalid-feedback"></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_donor_type" class="form-label">{{__('Donor Type')}}</label>
                        <select class="form-select" id="edit_donor_type" name="donor_type">
                            <option value="normal">{{__('Normal')}}</option>
                            <option value="monthly">{{__('Monthly')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_active" class="form-label">{{__('Status')}}</label>
                        <select class="form-select" id="edit_active" name="active">
                            <option value="1">{{__('Active')}}</option>
                            <option value="0">{{__('Inactive')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_monthly_donation_day" class="form-label">{{__('Monthly Donation Day')}}</label>
                        <input type="number" class="form-control" id="edit_monthly_donation_day" name="monthly_donation_day">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{__('Phone Numbers')}}</label>
                        <div id="edit-phone-container">
                            <!-- Phone inputs will be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>

<x-modal id="importDonorModal" title="{{__('Import Donors')}}">
    <form id="importDonorForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="donorFile" class="form-label">{{__('Upload Excel File')}}</label>
                <input type="file" class="form-control" id="donorFile" name="file" accept=".xlsx,.csv" required>
                <div id="fileError" class="text-danger mt-2" style="display: none;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-success">{{__('Import')}}</button>
        </div>
    </form>
    <div id="feedbackMessage" class="alert mt-3" style="display: none;"></div>
</x-modal>


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
            console.log(data);
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
                    addPhoneField(phoneContainer, phone.phone_number, phone.phone_type);
                });
            } else {
                addPhoneField(phoneContainer);
            }
        });
    }

    // Add phone field function
    function addPhoneField(container, phone = '', type = 'mobile') {
        const index = container.children().length;
        const phoneHtml = `
            <div class="input-group mb-2">
                <input type="text" name="phones[${index}][number]" class="form-control" placeholder="Enter phone number" value="${phone}">
                <select name="phones[${index}][type]" class="form-select" style="max-width: 150px;">
                    <option value="mobile" ${type === 'mobile' ? 'selected' : ''}}>{{__('Mobile')}}</option>
                    <option value="home" ${type === 'home' ? 'selected' : ''}}>{{__('Home')}}</option>
                    <option value="work" ${type === 'work' ? 'selected' : ''}}>{{__('Work')}}</option>
                    <option value="other" ${type === 'other' ? 'selected' : ''}}>{{__('Other')}}</option>
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
                    data: 'name',
                    name: 'name'
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
                {
                    data: 'governorate',
                    name: 'governorate.name'
                },
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
                        console.log(data);
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
                            console.log(area.id, selectedAreaId);
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
                    Object.keys(errors).forEach(function(key) {
                        var input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
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
                        errorDetails = errors.file ? errors.file[0] : 'Invalid file format.';
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
</script>
@endpush