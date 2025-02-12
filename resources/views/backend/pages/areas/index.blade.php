@extends('backend.layouts.master')
@section('title')
{{__('Areas')}}
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Area')}}
                    </button>
                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importAreaModal">
                        <i class="mdi mdi-upload"></i> {{__('Import Area')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Areas')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="areas-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('City')}}</th>
                                <th>{{__('Governorate')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Area Modal -->
@include('backend.pages.areas.add_modal')

<!-- Edit Area Modal -->
@include('backend.pages.areas.edit_modal')

<!-- Import Area Modal -->
@include('backend.pages.areas.import_modal')


@endsection

@push('scripts')
<script>
    // Load Cities based on Governorate
    function loadCities(governorateId, targetSelect, selectedCityId = null) {
        $.ajax({
            url: "{{ route('cities.by-governorate') }}",
            type: 'GET',
            data: {
                governorate_id: governorateId
            },
            success: function(response) {
                if (response.success) {
                    targetSelect.empty().append('<option value="">{{__('Select City ')}}</option>');
                    if (Array.isArray(response.data)) {
                        response.data.forEach(function(city) {
                            targetSelect.append(`<option value="${city.id}" ${city.id == selectedCityId ? 'selected' : ''}>${city.name}</option>`);
                        });
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading cities:', xhr);
            }
        });
    }

    // Edit Area Function
    function editArea(id, name, cityId) {
        var form = $('#editAreaForm');
        form.attr('action', `{{ route('areas.update', '') }}/${id}`);
        form.find('#edit_name').val(name);

        // Get city and governorate info
        $.get(`{{ url('areas') }}/${id}/edit`, function(data) {
            $('#edit_governorate_id').val(data.governorate_id);
            $('#edit_city_id').val(data.city_id);
            $('#edit_area_group_id').val(data.area_group_id);
            console.log(data);
            loadCities(data.governorate_id, $('#edit_city_id'), cityId);
        });

        $('#editAreaModal').modal('show');
    }

    let areasTable;
    $(function() {
        // Initialize DataTable



        areasTable = $('#areas-table').DataTable({
            ajax: "{{ route('areas.data') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'governorate',
                    name: 'governorate'
                },
                // {
                //     data: 'created_at',
                //     name: 'created_at'
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
            pageLength: 10,
            responsive: true,
            language: languages[language], // Apply language dynamically
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        // Governorate Change Event - Add Form
        $('#governorate_id').on('change', function() {
            loadCities($(this).val(), $('#city_id'));
        });

        // Governorate Change Event - Edit Form
        $('#edit_governorate_id').on('change', function() {
            loadCities($(this).val(), $('#edit_city_id'));
        });

        // Add Area Form Submit
        $('#addAreaForm').on('submit', function(e) {
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
                        $('#addAreaModal').modal('hide');
                        form[0].reset();
                        areasTable.ajax.reload();
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

        // Edit Area Form Submit
        $('#editAreaForm').on('submit', function(e) {
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
                        $('#editAreaModal').modal('hide');
                        form[0].reset();
                        areasTable.ajax.reload();
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

        $('#importAreaForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        let formData = new FormData(this);

        // Clear previous messages
        $('#feedbackMessage').hide().removeClass('alert-success alert-danger').text('');

        // AJAX request
        $.ajax({
            url: "{{ route('areas.import') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#importAreaForm button[type="submit"]').prop('disabled', true);
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
                        $('#importAreaModal').modal('hide');
                        $('#importAreaForm')[0].reset();
                    }, 2000);

                    areasTable.ajax.reload();
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
                $('#importAreaForm button[type="submit"]').prop('disabled', false);
            }
        });
    });


    });
</script>
@endpush