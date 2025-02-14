@extends('backend.layouts.master')

@section('title')
{{__('Events')}}
@endsection

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    
                </div>
                <h4 class="page-title">{{__('Events')}}</h4>


            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="header-title">{{__('Calendar')}}</h4>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="mdi mdi-plus"></i> {{__('Add Event')}}
                            </button>
                        </div>
                    </div>
                    <table id="events-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Title')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>


                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts') 
    <script>
        $(document).ready(function() {
            $('#events-table').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route('events.data') }}',
                },
                columns: [
                    { data: 'id' , name: 'id' },
                    { data: 'title' , name: 'title' },
                    { data: 'description' , name: 'description' },
                    { data: 'start_date' , name: 'start_date' },
                    { data: 'end_date' , name: 'end_date' },
                    { data: 'actions' , name: 'actions' }
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
            
        });
    </script>
    

    @endpush