@extends('backend.layouts.master')


@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{__('User Details')}}</h4>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <form method="GET" action="{{ route('users.details', $user->id) }}">
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-4">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            <button type="button" class="btn btn-secondary" onclick="clearFilters()">{{ __('Clear Filters') }}</button>
        </div>
    </div>
</form>


    <div class="row">

        <div class="col-lg-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-account-multiple widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Number of Customers">
                        {{__('Activities')}}
                    </h5>
                    <h3 class="mt-3 mb-3">{{$activities->count()}}</h3>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>



</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{ __('Donor Name') }}</th>
                        <th>{{__('Activity Type')}}</th>
                        <th>{{ __('Call Type') }}</th>
                        <th>{{ __('Date Time') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Response') }}</th>
                        <th>{{ __('Notes') }}</th>

                    </tr>
                    <tbody>
                        @foreach ($activities as $activity)
                        <tr>
                            <td>
                                {{ $activity->id }}
                            </td>
                            <td>
                                {{ $activity->donor->name }}
                            </td>
                            <td>
                                {{ $activity->activity_type }}
                            </td>
                            <td>
                                {{ $activity->callType->name }}
                            </td>
                            <td>
                                {{ $activity->date_time }}
                            </td>
                            <td>
                                {{ $activity->activity_status->name ?? '' }}
                            </td>
                            <td>
                                {{ $activity->response }}
                            </td>
                            <td>
                                {{ $activity->notes }}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    function clearFilters() {
        // Clear date inputs
        document.querySelector('input[name="start_date"]').value = '';
        document.querySelector('input[name="end_date"]').value = '';

        // Reload the page without query parameters
        window.location.href = "{{ route('users.details', $user->id) }}";
    }
</script>
@endpush
