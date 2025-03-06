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
                <div class="col-md-3">
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
                <div class="col-md-3 d-flex align-items-center">
                    <button class="btn btn-primary w-100" id="filterButton">{{ __('Filter') }}</button>
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
                                                <h5 class="font-14 my-1 fw-normal">{{__('Total Not Collected Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="notCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('Financial Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialNotCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="financialNotCollectedDonationsAmount">$</h5>
                                                <span class="text-muted font-13">{{__('Total')}}</span>
                                            </td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal">{{__('In Kind Donations')}}</h5>
                                            </td>
                                            <td>
                                                <h5 class="font-14 my-1 fw-normal" id="inKindNotCollectedDonationsCount"></h5>
                                                <span class="text-muted font-13">{{__('Count')}}</span>
                                            </td>
                                           
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

        function fetchDonations() {
            $.ajax({
                url: "{{ route('donations-report.not-collected') }}", // Adjust the route as needed
                method: "GET",
                data: {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    reporting_way: $('#reporting_way').val(),
                },
                success: function(response) {
                    $('#allDonations').text(response.allDonationsCount);

                    $('#allDonationsAmount').text(response.allDonationsAmount);
                    $('#notCollectedDonationsAmount').text(response.notCollectedDonationsAmount);
                    $('#notCollectedDonationsCount').text(response.notCollectedDonationsCount);
                    $('#notCollectedDonationsAmount').text(response.notCollectedDonationsAmount);

                    $('#financialNotCollectedDonationsCount').text(response.financialNotCollectedDonationsCount);
                    $('#financialNotCollectedDonationsAmount').text(response.financialNotCollectedDonationsAmount);
                    $('#inKindNotCollectedDonationsCount').text(response.inKindNotCollectedDonationsCount);
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