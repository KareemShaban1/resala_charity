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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonthlyDonationModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Monthly Donation')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Monthly Donations')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="monthly-donations-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Donor Name')}}</th>
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

<!-- Add Monthly Donation Modal -->
<x-modal id="addMonthlyDonationModal" title="{{__('Add Monthly Donation')}}" size="lg">
    <form id="addMonthlyDonationForm" method="POST" action="{{ route('monthly-donations.store') }}">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Donor Name')}}</label>
                        <select class="form-control select2" id="donor_id" name="donor_id" required>
                            <option value="">{{__('Select Donor')}}</option>
                            @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Donations Section -->
            <div class="card">
                <div class="card-header">
                    <h4>{{__('Financial Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="financial-donation-rows-container">
                        <!-- Rows for financial donations will be added here -->
                        <div class="row donation-row">
                            <input type="hidden" name="donation_type[]" value="financial">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="financial_donation_categories_id[]" required>
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
                                    <input type="number" class="form-control amount" name="financial_amount[]" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#financial-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- In-Kind Donations Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>{{__('In-Kind Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="in-kind-donation-rows-container">
                        <!-- Rows for in-kind donations will be added here -->
                        <div class="row donation-row">
                            <input type="hidden" name="donation_type[]" value="in-kind">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="in_kind_item_name[]" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                                    <input type="number" class="form-control" name="in_kind_quantity[]" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#in-kind-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="collecting_donation_way" class="form-label">{{__('Collecting Donation Way')}}</label>
                        <select class="form-control" name="collecting_donation_way" id="collecting_donation_way">
                            <option value="online">Online</option>
                            <option value="location">Location</option>
                            <option value="representative">representative</option>
                        </select>
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


<!-- Edit Monthly Donation Modal -->
<x-modal id="editMonthlyDonationModal" title="{{__('Edit Monthly Donation')}}">
    <form id="editMonthlyDonationForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        // Initialize DataTable
        var table = $('#monthly-donations-table').DataTable({
            ajax: "{{ route('monthly-donations.data') }}",
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

        // Handle Add Row buttons
        document.querySelectorAll('.add-row-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetContainer = document.querySelector(this.getAttribute('data-target'));
                const newRow = document.createElement('div');
                newRow.className = 'row donation-row';

                // Determine the type of donation (financial or in-kind)
                if (targetContainer.id === 'financial-donation-rows-container') {
                    newRow.innerHTML = `
                    <input type="hidden" name="donation_type[]" value="financial">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                            <select class="form-control donation-category" name="financial_donation_categories_id[]" required>
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
                            <input type="number" class="form-control amount" name="financial_amount[]" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-row-btn mt-2">{{__('Remove Row')}}</button>
                    </div>
                `;
                } else if (targetContainer.id === 'in-kind-donation-rows-container') {
                    newRow.innerHTML = `
                    <input type="hidden" name="donation_type[]" value="in-kind">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                            <input type="text" class="form-control" name="in_kind_item_name[]" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                            <input type="number" class="form-control" name="in_kind_quantity[]" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-row-btn mt-2">{{__('Remove Row')}}</button>
                    </div>
                `;
                }

                targetContainer.appendChild(newRow);

                // Add event listener to the new Remove Row button
                newRow.querySelector('.remove-row-btn').addEventListener('click', function() {
                    newRow.remove();
                });
            });
        });

        // Handle Remove Row buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row-btn')) {
                e.target.closest('.donation-row').remove();
            }
        });


        $('#donor_id').select2({
            dropdownParent: $('#addMonthlyDonationModal'),
            placeholder: '{{__('Select Donor ')}}',
            allowClear: true,
            width: '100%'
        });


        // Add Monthly Donation Form Submit
        $('#addMonthlyDonationForm').on('submit', function(e) {
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
                        $('#addMonthlyDonationModal').modal('hide');
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

        // Edit Monthly Donation Form Submit
        $('#editMonthlyDonationForm').on('submit', function(e) {
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
                        $('#editMonthlyDonationModal').modal('hide');
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

    // Edit Monthly Donation Function
    function editMonthlyDonation(id, name) {
        var form = $('#editMonthlyDonationForm');
        form.attr('action', `{{ route('monthly-donations.update', '') }}/${id}`);
        form.find('#edit_name').val(name);
        $('#editMonthlyDonationModal').modal('show');
    }

    $(document).ready(function () {
    $('#donor_id').select2({
        dropdownParent: $('#addMonthlyDonationModal'),
        placeholder: "{{__('Search by ID or Phone')}}",
        ajax: {
            url: '{{ route("donors.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    query: params.term // Search query
                };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function (donor) {
                        return {
                            id: donor.id,
                            text: `${donor.text}`, // Display the name and the exact phone that matched the search term
                        };
                    })
                };
            },
            // cache: true
        },
        templateResult: function (donor) {
            if (donor.loading) return donor.text;

            return $('<span>' + donor.text + '</span>'); // Display donor name and matched phone in the dropdown
        },
        templateSelection: function (donor) {
            return donor.text; // When selected, show name and matched phone
        }
    });
});

</script>
@endpush