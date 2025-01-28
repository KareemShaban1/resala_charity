@extends('backend.layouts.master')
@section('title')
{{__('Activity Logs')}}
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">{{__('Activity Logs')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="activity-logs-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Model ID</th>
                                <th>Changes</th>
                                <th>User</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#activity-logs-table').DataTable({
            ajax: "{{ route('activity-logs.data') }}",
            columns: [
            { data: 'id', name: 'id' },
            { data: 'action', name: 'action' },
            { data: 'model', name: 'model' },
            { data: 'model_id', name: 'model_id' },
            { 
                data: 'changes', 
                name: 'changes',
               
            },
            { 
                data: 'user.name', 
                name: 'user.name',
                defaultContent: 'System'
            },
            { data: 'created_at', name: 'created_at' },
        ],
            order: [
                [0, 'desc']
            ],
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Activity Logs Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                // {
                //     extend: 'pdf', 
                //     text: 'PDF', 
                //     title: 'Activity Logs Data', 
                //     exportOptions: {
                //         columns: [0, 1, 2, 3]
                //     }
                // },
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3,4,5,6]
                    }
                },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
            pageLength: 10,
            responsive: true,
            language: languages[language],
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

      
    });

</script>
@endpush