@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationCategoryModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Donation Category')}} 
                    </button>
                </div>
                <h4 class="page-title">{{__('Donation Categories')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="donation-categories-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
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

<!-- Add Donation Category Modal -->
<x-modal id="addDonationCategoryModal" title="{{__('Add Donation Category')}}">
    <form id="addDonationCategoryForm" method="POST" action="{{ route('donation-categories.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">{{__('Description')}}</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="active" name="active">
                    <label class="form-check-label" for="active">
                        {{__('Active')}}
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>

<!-- Edit Donation Category Modal -->
<x-modal id="editDonationCategoryModal" title="{{__('Edit Donation Category')}}">
    <form id="editDonationCategoryForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_description" class="form-label">{{__('Description')}}</label>
                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="edit_active" name="active">
                    <label class="form-check-label" for="edit_active">
                        {{__('Active')}}
                    </label>
                </div>
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
    $(function() {
        // Initialize DataTable
        var table = $('#donation-categories-table').DataTable({
            ajax: "{{ route('donation-categories.data') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        // Add Donation Category Form Submit
        $('#addDonationCategoryForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

            // Serialize form data
            var formData = form.serializeArray();

            var activeFieldFound = false; // To track if 'active' is found in formData

            // Find and update the 'active' value to integer (1 for checked, 0 for unchecked)
            formData.forEach(function(item) {
                if (item.name === 'active') {
                    item.value = (item.value === '1') ? 1 : 0; // '1' means checked, set it to 1 or 0
                    activeFieldFound = true; // Mark as found
                }
            });

            // If 'active' was not found in the form data (unchecked), push it with value 0
            if (!activeFieldFound) {
                formData.push({
                    name: 'active',
                    value: 0
                });
            }


            $.ajax({
                url: url,
                type: 'POST',
                data: $.param(formData), // Convert array to URL-encoded string
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#addDonationCategoryModal').modal('hide');
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



        // Edit Donation Category Form Submit
        $('#editDonationCategoryForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

            // Serialize form data
            var formData = form.serializeArray();

            // Add the `active` field explicitly
            var activeCheckbox = form.find('#edit_active');
            formData.push({
                name: 'active',
                value: activeCheckbox.is(':checked') ? 1 : 0
            });

            $.ajax({
                url: url,
                type: 'POST',
                data: $.param(formData), // Convert array to URL-encoded string
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#editDonationCategoryModal').modal('hide');
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

    // Edit Donation Category Function
    function editDonationCategory(item) {
        console.log(item);
        var form = $('#editDonationCategoryForm');

        // Update form action URL
        form.attr('action', `/donation-categories/${item.id}`);

        // Populate form fields with item data
        form.find('#edit_name').val(item.name);
        form.find('#edit_description').val(item.description || ''); // Handle empty description
        form.find('#edit_active').prop('checked', item.active === true);

        // Show the modal
        $('#editDonationCategoryModal').modal('show');
    }
</script>
@endpush