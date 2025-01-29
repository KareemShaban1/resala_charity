@extends('backend.layouts.master')
@section('title')
{{__('Backups')}}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <!-- <a href="{{route('backups.create')}}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> {{__('Add Backup')}}
                    </a> -->
                <form action="{{ route('backups.create') }}" method="GET" class="add-new-backup"
                    enctype="multipart/form-data" id="CreateBackupForm">
                    {{ csrf_field() }}
                    <input type="submit" name="submit" class="theme-button btn btn-primary pull-right"
                        style="margin-bottom:2em;" value="{{ __('Add Backup') }}">
                </form>
            </div>
            <h4 class="page-title">{{__('Backups')}}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <table id="backups-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{__('Filename')}}</th>
                            <th>{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#backups-table').DataTable({
            ajax: "{{ route('backups.data') }}",
            columns: [{
                    data: 'filename',
                    name: 'filename'
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
            buttons: [],
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