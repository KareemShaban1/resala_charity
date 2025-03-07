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
            <input type="date" id="start_date" class="form-control" placeholder="{{ __('Start Date') }}">
        </div>
        <div class="col-md-3">
            <input type="date" id="end_date" class="form-control" placeholder="{{ __('End Date') }}">
        </div>
        <div class="col-md-3">
            <button id="filterBtn" class="btn btn-primary">{{ __('Filter') }}</button>
        </div>
    </div>


    <!-- Activity Types Statistics Section -->
    <div class="row mb-4" id="activityStatusesSection">
        <!-- Activity types will be dynamically inserted here -->
    </div>

    <!-- Activity Types Statistics Section -->
    <div class="row mb-4" id="activityTypesSection">
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
                url: '{{ route("donor-report.donorActivities") }}',
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.user_id = $('#user_filter').val();
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
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'department',
                    name: 'department'
                },
                {
                    data: 'activities_count',
                    name: 'activities_count'
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
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Donors Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
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

        function fetchStatistics() {
            $.ajax({
                url: '{{ route("donor-report.statistics") }}',
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val()
                },
                success: function(response) {

                    // Populate activity types dynamically
                    let activityTypesHtml = '';
                    if (response.activity_types) {
                        Object.keys(response.activity_types).forEach(function(activityType) {
                            activityTypesHtml += `
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>${activityType}</h5>
                                        <h3>${response.activity_types[activityType]}</h3>
                                    </div>
                                </div>
                            </div>
                        `;
                        });
                    }
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

        // Initial statistics fetch
        fetchStatistics();
    });
</script>
@endpush
@endsection