@extends('backend.layouts.master')

@section('title')
{{ __('Donations Reports') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Donations Reports') }}</h4>
            </div>

            <!-- Filters -->
            <div class="row mb-3">

                <div class="col-md-3">
                    <label for="start_date">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="department_id">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach (App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-md-2">
                    <label for="employee_id">{{ __('Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach (App\Models\Employee::all() as $empployee)
                            <option value="{{ $empployee->id }}">{{ $empployee->name }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="filterButton">{{ __('Filter') }}</button>
                </div>
            </div>

            <!-- Donation Stats -->
            <div class="row">

                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <a href="dashboard.export_reports" class="btn btn-sm btn-link float-end">Export
                                <i class="mdi mdi-download ms-1"></i>
                            </a>
                            <h4 class="header-title mt-2 mb-3">{{__('Donations Report')}}</h4>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <tbody>
                                        <tr class="text-info">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Total Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="allDonations"></h5>
                                                <span class="text-muted font-13">{{__('Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="allDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <!-- <tr class="text-success">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonations"></h5>
                                                <span class="text-muted font-13">{{__('Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Not Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonations"></h5>
                                                <span class="text-muted font-13">{{__('Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <a href="dashboard.export_reports" class="btn btn-sm btn-link float-end">Export
                                <i class="mdi mdi-download ms-1"></i>
                            </a>
                            <h4 class="header-title mt-2 mb-3">{{__('Collected Donations Report')}}</h4>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <tbody>
                                        <tr class="text-info">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Total Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Financial Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Financial Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('In Kind Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('In Kind Donations Count')}}</span>
                                            </td>
                                            <!-- <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <a href="dashboard.export_reports" class="btn btn-sm btn-link float-end">Export
                                <i class="mdi mdi-download ms-1"></i>
                            </a>
                            <h4 class="header-title mt-2 mb-3">{{__('Not Collected Donations Report')}}</h4>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <tbody>
                                        <tr class="text-info">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Total Not Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Financial Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialNotCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Financial Donations Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialNotCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('In Kind Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindNotCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('In Kind Donations Count')}}</span>
                                            </td>
                                            <!-- <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindNotCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Get today's date in YYYY-MM-DD format
        let today = new Date().toISOString().split('T')[0];

        // Set the default value of the date inputs
        $('#start_date').val(today);
        $('#end_date').val(today);

        function fetchDonations(startDate = today, endDate = today) {
            $.ajax({
                url: "{{ route('donations-report.index') }}", // Adjust the route as needed
                method: "GET",
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    employee_id: $('#employee_id').val(),
                    department_id: $('#department_id').val(),
                },
                success: function(response) {
                    $('#allDonations').text(response.allDonationsCount);
                    // $('#collectedDonations').text(response.collectedDonationsCount);
                    // $('#notCollectedDonations').text(response.notCollectedDonationsCount);

                    $('#allDonationsAmount').text(response.allDonationsAmount);
                    $('#collectedDonationsAmount').text(response.collectedDonationsAmount);
                    $('#notCollectedDonationsAmount').text(response.notCollectedDonationsAmount);
                    $('#collectedDonationsCount').text(response.collectedDonationsCount);
                    $('#collectedDonationsAmount').text(response.collectedDonationsAmount);
                    $('#financialCollectedDonationsCount').text(response.financialCollectedDonationsCount);
                    $('#financialCollectedDonationsAmount').text(response.financialCollectedDonationsAmount);
                    $('#inKindCollectedDonationsCount').text(response.inKindCollectedDonationsCount);
                    // $('#inKindCollectedDonationsAmount').text(response.inKindCollectedDonationsAmount);
                    $('#notCollectedDonationsCount').text(response.notCollectedDonationsCount);
                    $('#notCollectedDonationsAmount').text(response.notCollectedDonationsAmount);

                    $('#financialNotCollectedDonationsCount').text(response.financialNotCollectedDonationsCount);
                    $('#financialNotCollectedDonationsAmount').text(response.financialNotCollectedDonationsAmount);
                    $('#inKindNotCollectedDonationsCount').text(response.inKindNotCollectedDonationsCount);
                    // $('#inKindNotCollectedDonationsAmount').text(response.inKindNotCollectedDonationsAmount);
                }
            });
        }

        // Fetch data on page load with today's date
        fetchDonations();

        // Fetch data when the filter button is clicked
        $('#filterButton').click(function() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            fetchDonations(startDate, endDate);
        });

    });
</script>
@endpush