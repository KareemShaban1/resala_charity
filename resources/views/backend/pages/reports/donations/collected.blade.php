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
                <div class="col-md-9">

                    <div class="row">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="department_id" class="form-label">{{ __('Department') }}</label>
                            <select name="department_id" id="department_id" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (App\Models\Department::all() as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <label for="employee_id" class="form-label">{{ __('Employee') }}</label>
                            <select name="employee_id" id="employee_id" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (App\Models\Employee::all() as $empployee)
                                <option value="{{ $empployee->id }}">{{ $empployee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="reporting_way" class="form-label">{{__('Activity Way')}}</label>
                                <select class="form-control" name="reporting_way" id="reporting_way">
                                    <option value="">{{__('All')}}</option>
                                    <option value="call">{{__('Call')}}</option>
                                    <option value="whatsapp_chat">{{__('Whatsapp Chat')}}</option>
                                    <option value="location">{{__('Location')}}</option>
                                    <option value="other">{{__('Other')}}</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="collecting_way" class="form-label">{{__('Collecting Way')}}</label>
                                <select name="collecting_way" id="collecting_way" class="form-control">
                                    <option value="">{{__('All')}}</option>
                                    <option value="representative">{{__('Representative')}}</option>
                                    <option value="location">{{__('Location')}}</option>
                                    <option value="online">{{__('Online')}}</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex flex-wrap align-items-center">
                    <button class="btn btn-primary w-100" id="filterButton">{{ __('Filter') }}</button>
                    <button class="btn btn-danger w-100" id="clearButton">{{ __('Clear') }}</button>

                </div>
            </div>

            <!-- Donation Stats -->
            <div class="row">

                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <div class="card">
                        <div class="card-body">

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
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="allDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>

                                        <tr class="text-warning">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Total Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="collectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Financial Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('In Kind Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>

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

                            <h4 class="header-title mt-2 mb-3">{{__('Donations Categories Report')}}</h4>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0 collected-donations-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Donation Category') }}</th>
                                            <th>{{ __('Count') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be inserted here by JavaScript -->
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
        // $('#start_date').val(today);
        // $('#end_date').val(today);

        function fetchDonations() {
            $.ajax({
                url: "{{ route('donations-report.collected') }}", // Adjust the route as needed
                method: "GET",
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    employee_id: $('#employee_id').val(),
                    department_id: $('#department_id').val(),
                    reporting_way: $('#reporting_way').val(),
                    collecting_way: $('#collecting_way').val(),
                },
                success: function(response) {
                    $('#allDonations').text(response.allDonationsCount);
                    // $('#collectedDonations').text(response.collectedDonationsCount);

                    $('#allDonationsAmount').text(response.allDonationsAmount);
                    $('#collectedDonationsAmount').text(response.collectedDonationsAmount);
                    $('#collectedDonationsCount').text(response.collectedDonationsCount);
                    $('#collectedDonationsAmount').text(response.collectedDonationsAmount);
                    $('#financialCollectedDonationsCount').text(response.financialCollectedDonationsCount);
                    $('#financialCollectedDonationsAmount').text(response.financialCollectedDonationsAmount);
                    $('#inKindCollectedDonationsCount').text(response.inKindCollectedDonationsCount);

                    // Clear the table before appending new data
                    let tableBody = $('.collected-donations-table tbody');
                    tableBody.empty();

                    // Append donation categories dynamically
                    $.each(response.donationsByCategory, function(category, data) {
                        tableBody.append(`
                            <tr>
                                <td><strong>${category}</strong></td>
                                <td>${data.count}</td>
                                <td>$${data.total_amount}</td>
                            </tr>
                        `);
                    });
                }
            });
        }

        // Fetch data on page load with today's date
        fetchDonations();

        // Fetch data when the filter button is clicked
        $('#filterButton').click(function() {
            fetchDonations();
        });
        $('#clearButton').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#department_id').val('');
            $('#employee_id').val('');
            $('#reporting_way').val('');
            $('#collecting_way').val('');
            fetchDonations();
        });

    });
</script>
@endpush