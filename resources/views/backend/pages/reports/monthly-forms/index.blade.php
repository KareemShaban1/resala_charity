@extends('backend.layouts.master')

@section('title')
{{ __('Monthly Forms Reports') }}
@endsection

@section('content')
<div class="container-fluid">
    <!-- <div class="card"> -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('Monthly Forms Reports') }}</h4>
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


            <!-- Table -->
            <div class="row" id="filteredData">
                @include('backend.pages.reports.monthly-forms.partials.monthly_forms_table', [
                'allMonthlyFormsCount' => $allMonthlyFormsCount,
                'allMonthlyFormsAmount' => $allMonthlyFormsAmount,
                'monthlyFormsCollectedCount' => $monthlyFormsCollectedCount,
                'monthlyFormsCollectedAmount' => $monthlyFormsCollectedAmount,
                'monthlyFormsNotCollectedCount' => $monthlyFormsNotCollectedCount,
                'monthlyFormsNotCollectedAmount' => $monthlyFormsNotCollectedAmount
                ])
            </div>


            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <h5 class="text-success">{{ __('Donors with Collected Monthly Forms') }}</h5>
                            <div id="collectedDonorsTable">
                                @include('backend.pages.reports.monthly-forms.partials.donors_collected_table', ['donorsWithCollectedForms' => $donorsWithCollectedForms])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-danger">{{ __('Donors with Not Collected Monthly Forms') }}</h5>
                            <div id="notCollectedDonorsTable">
                                @include('backend.pages.reports.monthly-forms.partials.donors_not_collected_table', ['donorsWithNotCollectedForms' => $donorsWithNotCollectedForms])
                            </div>
                        </div> -->

                        <div class="col-md-12">
                            <h5 class="text-info">{{ __('Donors with Monthly Forms') }}</h5>
                            <div id="donorsWithFormsTable">
                                @include('backend.pages.reports.monthly-forms.partials.donors_with_forms_table', ['donorsWithForms' => $donorsWithForms])
                                <!-- Pagination Links -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $donorsWithForms->links() }}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
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
                url: "{{ route('monthly-forms-report.filter') }}",
                method: "GET",
                data: {
                    month_year: month_year,
                    from_date: from_date,
                    to_date: to_date
                },
                success: function(response) {
                    $('#filteredData').html(response.filteredTable);
                    // $('#collectedDonorsTable').html(response.collectedDonorsTable);
                    // $('#notCollectedDonorsTable').html(response.notCollectedDonorsTable);
                    $('#donorsWithFormsTable').html(response.donorsWithFormsTable);

                }
            });
        });
    });
</script>
@endpush