@extends('backend.layouts.master')

@section('title')
    {{ __('Events') }}
@endsection

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Events') }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="header-title">{{ __('Calendar') }}</h4>
                        </div>
                    </div>

                    <table id="events-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editEventForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Event') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editEventId">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" id="editTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea id="editDescription" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" id="editStartDate" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" id="editEndDate" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete this event?') }}</p>
                    <input type="hidden" id="deleteEventId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var table = $('#events-table').DataTable({
            serverSide: true,
            processing: true,
            ajax: '{{ route('events.data') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'description', name: 'description' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
        });

        // Open Edit Modal & Populate Fields
        $(document).on('click', '.edit-button', function () {
            var eventId = $(this).data('id');
            $.get('/events/' + eventId, function (data) {
                $('#editEventId').val(data.id);
                $('#editTitle').val(data.title);
                $('#editDescription').val(data.description);
                $('#editStartDate').val(data.start_date);
                $('#editEndDate').val(data.end_date);
                $('#editEventModal').modal('show');
            });
        });

        // Handle Edit Event Form Submission
        $('#editEventForm').submit(function (e) {
            e.preventDefault();
            var id = $('#editEventId').val();
            $.ajax({
                url: '/events/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: $('#editTitle').val(),
                    description: $('#editDescription').val(),
                    start_date: $('#editStartDate').val(),
                    end_date: $('#editEndDate').val(),
                },
                success: function (response) {
                    if (response.success) {
                    $('#editEventModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                }
                },
            });
        });

        // Open Delete Modal
        $(document).on('click', '.delete-button', function () {
            var eventId = $(this).data('id');
            $('#deleteEventId').val(eventId);
            $('#deleteEventModal').modal('show');
        });

        // Handle Delete Confirmation
        $('#confirmDelete').click(function () {
            var id = $('#deleteEventId').val();
            $.ajax({
                url: '/events/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        $('#deleteEventModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });}
                },
            });
        });
    });
</script>
@endpush
