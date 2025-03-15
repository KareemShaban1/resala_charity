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
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="action-filter">{{ __('Action') }}</label>
            <select id="action-filter" class="form-control">
                <option value="">{{ __('All') }}</option>
                <option value="created">{{ __('created') }}</option>
                <option value="updated">{{ __('updated') }}</option>
                <option value="deleted">{{ __('deleted') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="model-filter">{{ __('Model') }}</label>
            <select id="model-filter" class="form-control">
                <option value="">{{ __('All') }}</option>
                <option value="App\Models\Governorate">{{ __('Governorate') }}</option>
                <option value="App\Models\City">{{ __('City') }}</option>
                <option value="App\Models\Area">{{ __('Area') }}</option>
                <option value="App\Models\AreaGroup">{{ __('Area Group') }}</option>
                <option value="App\Models\CallType">{{ __('Call Type') }}</option>
                <option value="App\Models\ActivityStatus">{{ __('Activity Status') }}</option>
                <option value="App\Models\DonorActivity">{{ __('Donor Activity') }}</option>
                <option value="App\Models\DonorPhone">{{ __('Donor Phone') }}</option>
                <option value="App\Models\Donor">{{ __('Donor') }}</option>
                <option value="App\Models\Employee">{{ __('Employee') }}</option>
                <option value="App\Models\Department">{{ __('Department') }}</option>
                <option value="App\Models\User">{{ __('User') }}</option>
                <option value="App\Models\Event">{{ __('Event') }}</option>
                <option value="App\Models\DonationItem">{{ __('Donation Item') }}</option>
                <option value="App\Models\DonationCategory">{{ __('Donation Category') }}</option>
                <option value="App\Models\Donation">{{ __('Donation') }}</option>
                <option value="App\Models\DonationCollecting">{{ __('Donation Collecting') }}</option>
                <option value="App\Models\MonthlyFormItem">{{ __('Monthly Form Item') }}</option>
                <option value="App\Models\MonthlyForm">{{ __('Monthly Form') }}</option>
                <option value="App\Models\CollectingLine">{{ __('Collecting Lines') }}</option>
                <option value="Spatie\Permission\Models\Role">{{ __('Roles') }}</option>
                
                <!-- Add more models as needed -->
            </select>
        </div>

        <div class="col-md-3">
            <label for="user-filter">{{ __('User') }}</label>
            <select id="user-filter" class="form-control">
                <option value="">{{ __('All') }}</option>
                @foreach(App\Models\User::all() as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="date-filter">{{ __('Date') }}</label>
            <input type="text" id="date-filter" class="form-control" placeholder="{{ __('Select Date Range') }}">
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="activity-logs-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Model') }}</th>
                                <th>{{ __('Model ID') }}</th>
                                <th>{{ __('Changes') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Date Time') }}</th>
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
    var table = $('#activity-logs-table').DataTable({
        ajax: {
            url: "{{ route('activity-logs.data') }}",
            data: function(d) {
                d.action = $('#action-filter').val();
                d.model = $('#model-filter').val();
                d.user = $('#user-filter').val();
                d.date_range = $('#date-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id', searchable: false },
            { data: 'action', name: 'action' },
            { data: 'model', name: 'model' },
            { data: 'model_id', name: 'model_id', searchable: true },
            { data: 'changes', name: 'changes' },
            { data: 'user.name', name: 'user.name', defaultContent: 'System' },
            { data: 'created_at', name: 'created_at' },
        ],
        order: [[0, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
        pageLength: 10,
        responsive: true,
        language: languages[language],
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    // Apply filters
    $('#action-filter, #model-filter, #user-filter').change(function() {
        table.ajax.reload();
    });

    // Date Range Picker
    $('#date-filter').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    });

    $('#date-filter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.ajax.reload();
    });
});

</script>
@endpush