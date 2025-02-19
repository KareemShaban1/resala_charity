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

    <!-- Statistics Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>{{__('Reply And Donate')}}</h5>
                    <h3 id="replyDonateCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>{{__('Reply And Not Donate')}}</h5>
                    <h3 id="replyNotDonateCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>{{__('No Reply')}}</h5>
                    <h3 id="noReplyCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>{{__('Phone Not Available')}}</h5>
                    <h3 id="phoneNotAvailableCount">0</h3>
                </div>
            </div>
        </div>
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
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'department', name: 'department' },
            { data: 'activities_count', name: 'activities_count' }
        ],
        order: [[0, 'desc']],
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
                        columns: [0, 1, 2, 3,4]
                    }
                },

                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            lengthMenu: [[10, 25, 50, 100, 500, 1000, 2000], [10, 25, 50, 100, 500, 1000, 2000]], 
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
                $('#replyDonateCount').text(response.statistics.ReplyAndDonate || 0);
                $('#replyNotDonateCount').text(response.statistics.ReplyAndNotDonate || 0);
                $('#noReplyCount').text(response.statistics.NoReply || 0);
                $('#phoneNotAvailableCount').text(response.statistics.PhoneNotAvailable || 0);
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