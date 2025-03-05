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
                        <label for="department_id">{{ __('Department') }}</label>
                        <select name="department_id" id="department_id" class="form-control">
                            <option value="">{{ __('All Departments') }}</option>
                            @foreach (App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div class="col-md-3">
                        <label for="from_date">{{ __('From Date') }}</label>
                        <input type="date" name="from_date" id="from_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">{{ __('To Date') }}</label>
                        <input type="date" name="to_date" id="to_date" class="form-control">
                    </div> -->
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
                'monthlyFormsNotCollectedAmount' => $monthlyFormsNotCollectedAmount,
                'cancelledMonthlyFormsCount' => $cancelledMonthlyFormsCount,
                'cancelledMonthlyFormsAmount' => $cancelledMonthlyFormsAmount,
                ])
            </div>


            <div class="card">
                <div class="card-body">
                    <div class="row">
                       

                        <div class="col-md-12">
                            <h5 class="text-info">{{ __('Donors with Monthly Forms') }}</h5>
                            <div id="donorsWithFormsTable">
                                @include('backend.pages.reports.monthly-forms.partials.donors_with_forms_table', ['donorsWithForms' => $donorsWithForms])
                                <!-- Pagination Links -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $donorsWithForms->links('pagination::bootstrap-4') }}
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
            // let from_date = $('#from_date').val();
            // let to_date = $('#to_date').val();
            let department_id = $('#department_id').val();

            $.ajax({
                url: "{{ route('monthly-forms-report.filter') }}",
                method: "GET",
                data: {
                    month_year: month_year,
                    // from_date: from_date,
                    // to_date: to_date,
                    department_id: department_id
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