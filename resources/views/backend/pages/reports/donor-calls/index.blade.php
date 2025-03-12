@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Users') }}</h4>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="start_date"  class="form-label">{{ __('Start Date') }}</label>
            <input type="date" id="start_date" class="form-control" placeholder="{{ __('Start Date') }}">
        </div>
        <div class="col-md-3">
            <label for="end_date"  class="form-label">{{ __('End Date') }}</label>
            <input type="date" id="end_date" class="form-control" placeholder="{{ __('End Date') }}">
        </div>
        @if (Auth::user()->is_super_admin)
        <div class="col-md-2">
            <label for="user_filter" class="form-label">{{ __('Users') }}</label>
            <select id="user_filter" class="form-control">
                <option value="">{{ __('All Users') }}</option>
                @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="department_filter" class="form-label">{{ __('Departments') }}</label>
            <select id="department_filter" class="form-control">
                <option value="">{{ __('All Departments') }}</option>
                @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="call_type_filter" class="form-label">{{ __('Call Types') }}</label>
            <select id="call_type_filter" class="form-control">
                <option value="">{{ __('All Call Types') }}</option>
                @foreach ($callTypes as $callType)
                <option value="{{ $callType->id }}">{{ $callType->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-md-3">
            <button id="filterBtn" class="btn btn-primary">{{ __('Filter') }}</button>
            <button id="clearBtn" class="btn btn-secondary">{{ __('Clear') }}</button>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row mb-4" id="activityStatusesSection">
        <!-- Activity types will be dynamically inserted here -->
    </div>


    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="users-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>

                                </th> <!-- Expand button -->
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Department') }}</th>
                                <th>{{ __('Activities Count') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("donor-report.donor-calls") }}',
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.user_id = $('#user_filter').val();
                    d.department_id = $('#department_filter').val();
                    d.call_type_id = $('#call_type_filter').val();
                }
            },
            columns: [{
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="mdi mdi-plus"></i> '
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'department',
                    name: 'department'
                },
                {
                    data: 'filtered_activities_count',
                    name: 'filtered_activities_count'
                }
            ],
            order: [
                [1, 'desc']
            ],
            search: {
                regex: true
            },
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Donors Data',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000, 2000],
                [10, 25, 50, 100, 500, 1000, 2000]
            ],
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            },
        });

        // Add click event for expanding user activities
        $('#users-table tbody').on('click', 'td.dt-control', function() {
            let tr = $(this).closest('tr');
            let row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                let data = row.data();

                // Generate the activities table
                let activitiesHtml = `<table class="table table-bordered">
                <thead>
                <tr>
                <th>#</th>
                <th>Donor Name</th>
                <th>Activity Type</th>
                <th>Call Type</th>
                <th>Date Time</th>
                <th>Status</th>
                <th>Response</th>
                <th>Notes</th>
                </tr>
                </thead>
                <tbody>`;
                if (data.activities.length) {
                    data.activities.forEach((activity, index) => {
                        activitiesHtml += `<tr><td>${index + 1}</td>
                        <td>${activity.donor?.name}</td>
                        <td>${activity.activity_type}</td>
                        <td>${activity.call_type?.name}</td>
                        <td>${activity.date_time}</td>
                        <td>
                            ${activity.activity_status?.name ?? ''}
                        </td>
                        <td>${activity.response ?? ''}</td>
                        <td>${activity.notes ?? ''}</td>
                        </tr>`;
                    });
                } else {
                    activitiesHtml += '<tr><td colspan="3">No activities found</td></tr>';
                }
                activitiesHtml += '</tbody></table>';

                row.child(activitiesHtml).show();
                tr.addClass('shown');
            }
        });


        function fetchStatistics() {
            $.ajax({
                url: '{{ route("donor-report.calls-statistics") }}',
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    user_id: $('#user_filter').val(),
                    department_id: $('#department_filter').val(),
                    call_type_id: $('#call_type_filter').val(),

                },
                success: function(response) {

                    let activityStatusesHtml = '';
                    if (response.statistics) {
                        Object.keys(response.statistics).forEach(function(status) {
                            activityStatusesHtml += `
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>${status}</h5>
                        <h3>${response.statistics[status]}</h3>
                    </div>
                </div>
            </div>
        `;
                        });
                    }
                    $('#activityStatusesSection').html(activityStatusesHtml);
                }
            });
        }

        $('#filterBtn').click(function() {
            table.ajax.reload();
            fetchStatistics();
        });
        $('#clearBtn').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#user_filter').val('');
            $('#department_filter').val('');
            $('#call_type_filter').val('');
            table.ajax.reload();
            fetchStatistics();
        });

        // Initial statistics fetch
        fetchStatistics();
    });
</script>
@endpush
@endsection