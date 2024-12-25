@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Employee')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Employees')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="employees-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Department')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<x-modal id="addEmployeeModal" title="{{__('Add Employee')}}">
    <form id="addEmployeeForm" method="POST" action="{{ route('employees.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">{{__('Department')}}</label>
                <select class="form-control" id="department_id" name="department_id" required>
                    <option value="">{{__('Select Department') }}</option>
                    @foreach(\App\Models\Department::all() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
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

<!-- Edit Employee Modal -->
<x-modal id="editEmployeeModal" title="{{__('Edit Employee')}}">
    <form id="editEmployeeForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_department_id" class="form-label">{{__('Department')}}</label>
                <select class="form-control" id="edit_department_id" name="department_id" required>
                    <option value="">{{__('Select Department') }}</option>
                    @foreach(\App\Models\Department::all() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
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
    $(function () {
        // Initialize DataTable
        var table = $('#employees-table').DataTable({
            ajax: "{{ route('employees.data') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'department', name: 'department.name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        // Add Employee Form Submit
        $('#addEmployeeForm').on('submit', function(e) {
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
                        $('#addEmployeeModal').modal('hide');
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

        // Edit Employee Form Submit
        $('#editEmployeeForm').on('submit', function(e) {
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
                        $('#editEmployeeModal').modal('hide');
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

    // Edit Employee Function
    function editEmployee(id, name, department_id) {
        var form = $('#editEmployeeForm');
        form.attr('action', `{{ route('employees.update', '') }}/${id}`);
        form.find('#edit_name').val(name);
        form.find('#edit_department_id').val(department_id);
        $('#editEmployeeModal').modal('show');
    }
</script>
@endpush