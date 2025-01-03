@extends('backend.layouts.master')

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
                                <!-- <th>{{__('Created At')}}</th> -->
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
<x-modal id="addAreaModal" title="{{__('Add New Area')}}">
    <form id="addAreaForm" method="POST" action="{{ route('areas.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="governorate_id" class="form-label">{{__('Governorate')}}</label>
                <select class="form-control select2" id="governorate_id" required>
                    <option value="">{{__('Select Governorate')}}</option>
                    @foreach(\App\Models\Governorate::all() as $governorate)
                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="city_id" class="form-label">{{__('City')}}</label>
                <select class="form-control select2" id="city_id" name="city_id" required>
                    <option value=""> {{__('Select City')}}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>

<!-- Edit Area Modal -->
<x-modal id="editAreaModal" title="Edit Area">
    <form id="editAreaForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_governorate_id" class="form-label">{{__('Governorate')}}</label>
                <select class="form-control" id="edit_governorate_id" required>
                    <option value="">{{__('Select Governorate')}}</option>
                    @foreach(\App\Models\Governorate::all() as $governorate)
                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="edit_city_id" class="form-label">{{__('City')}}</label>
                <select class="form-control" id="edit_city_id" name="city_id" required>
                    <option value="">{{__('Select City')}}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>

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
            console.log(data);
            loadCities(data.governorate_id, $('#edit_city_id'), cityId);
        });

        $('#editAreaModal').modal('show');
    }

    $(function() {
        // Initialize DataTable



        var table = $('#areas-table').DataTable({
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
                    name: 'city.name'
                },
                {
                    data: 'governorate',
                    name: 'city.governorate.name'
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
</script>
@endpush