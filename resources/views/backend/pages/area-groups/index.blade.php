@extends('backend.layouts.master')
@section('title')
{{__('Areas Groups')}}
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaGroupModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Areas Group')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Areas Groups')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="areas-groups-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Areas')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add AreaGroup Modal -->
<x-modal id="addAreaGroupModal" title="{{__('Add New Area Group')}}">
    <form id="addAreaGroupForm" method="POST" action="{{ route('areas-groups.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="areas" class="form-label">{{__('Areas')}}</label>
                <select class="form-control select2" id="areas" name="areas[]" multiple required>
                    <option value="">{{__('Select Area')}}</option>
                    @foreach(\App\Models\Area::all() as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>

<!-- Edit AreaGroup Modal -->
<x-modal id="editAreaGroupModal" title="Edit Area Group">
    <form id="editAreaGroupForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_areas" class="form-label">{{__('Areas')}}</label>
                <select class="form-control" id="edit_areas" name="areas[]" multiple required>
                    <option value="">{{__('Select Area')}}</option>
                    @foreach(\App\Models\Area::all() as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Edit AreaGroup Function
    function editAreaGroup(id, name) {
        var form = $('#editAreaGroupForm');
        form.attr('action', `{{ route('areas-groups.update', '') }}/${id}`);
        form.find('#edit_name').val(name);

        // Fetch area group data via AJAX
        $.get(`{{ url('areas-groups') }}/${id}/edit`, function(response) {
            if (response.success) {
                console.log(response);

                // Extract area IDs from the response
                var areaIds = response.data.areas.map(function(area) {
                    return area.id;
                });

                // Clear previous selections
                $('#edit_areas').val(null).trigger('change');

                // Pre-select the associated areas using the extracted IDs
                $('#edit_areas').val(areaIds).trigger('change');
            }
        });

        // Show the modal
        $('#editAreaGroupModal').modal('show');
    }

    $(function() {
        // Initialize DataTable



        var table = $('#areas-groups-table').DataTable({
            ajax: "{{ route('areas-groups.data') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'areas',
                    name: 'areas'
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
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Area Groups Data',
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


        // Add AreaGroup Form Submit
        $('#addAreaGroupForm').on('submit', function(e) {
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
                        $('#addAreaGroupModal').modal('hide');
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

        // Edit AreaGroup Form Submit
        $('#editAreaGroupForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

                // Log the form data
            console.log(form.serialize());
            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#editAreaGroupModal').modal('hide');
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

    $('#areas').select2({
    dropdownParent: $('#addAreaGroupModal'), // Append dropdown to the modal
    placeholder: '{{__('Select Areas')}}',
    allowClear: true,
    width: '100%'
});

    // Initialize Select2 when the modal is fully shown
    $('#editAreaGroupModal').on('shown.bs.modal', function() {
        $('#edit_areas').select2({
            dropdownParent: $('#editAreaGroupModal'), // Append dropdown to the modal
            placeholder: '{{__('Select Areas')}}',
            allowClear: true,
            width: '100%'
        });
    });

    // Destroy Select2 when the modal is hidden to avoid conflicts
    $('#editAreaGroupModal').on('hidden.bs.modal', function() {
        $('#edit_areas').select2('destroy');
    });
</script>
@endpush