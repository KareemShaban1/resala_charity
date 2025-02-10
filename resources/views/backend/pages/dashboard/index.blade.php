@extends('backend.layouts.master')

@section('title')
{{ __('Dashboard') }}
@endsection

@section('content')
<div class="container-fluid">
    <!-- <div class="card"> -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Dashboard') }}</h4>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label for="month_year">{{ __('Select Month') }}</label>
                        <input type="month" name="month_year" id="month_year" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="from_date">{{ __('From Date') }}</label>
                        <input type="date" name="from_date" id="from_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">{{ __('To Date') }}</label>
                        <input type="date" name="to_date" id="to_date" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="filterButton">{{ __('Filter') }}</button>
                    </div>
                </div>

            </div>


            <div class="col-xl-6 col-lg-6">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-multiple widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Number of Customers">
                                    {{__('Donors')}}
                                </h5>
                                <h3 class="mt-3 mb-3">{{$donorsCount}}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 5.27%</span>
                                    <span class="text-nowrap">Since last month</span>
                                </p>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cart-plus widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Orders</h5>
                                <h3 class="mt-3 mb-3">5,543</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 1.08%</span>
                                    <span class="text-nowrap">Since last month</span>
                                </p>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-currency-usd widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Revenue</h5>
                                <h3 class="mt-3 mb-3">$6,254</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 7.00%</span>
                                    <span class="text-nowrap">Since last month</span>
                                </p>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-pulse widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Growth">Growth</h5>
                                <h3 class="mt-3 mb-3">+ 30.56%</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                                    <span class="text-nowrap">Since last month</span>
                                </p>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row -->

            </div>






        </div>
    </div>
    <!-- </div> -->
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Set default value for month_year as current month (YYYY-MM)
        let currentMonthYear = new Date().toISOString().slice(0, 7); // Format: YYYY-MM
        $('#month_year').val(currentMonthYear);
        $('#filterButton').on('click', function() {
            let month_year = $('#month_year').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            $.ajax({
                url: "{{ route('dashboard.filter') }}",
                method: "GET",
                data: {
                    month_year: month_year,
                    from_date: from_date,
                    to_date: to_date
                },
                success: function(response) {
                    $('#filteredData').html(response);
                }
            });
        });
    });
</script>
@endpush