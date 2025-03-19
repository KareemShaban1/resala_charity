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
                    <div class="col-md-3">
                        <label for="follow_up_department_id">{{ __('Follow Up Department') }}</label>
                        <select name="follow_up_department_id" id="follow_up_department_id" class="form-control">
                            <option value="">{{ __('All Departments') }}</option>
                            @foreach (App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
                                <div class="d-flex justify-content-center mt-3 pagination-links">
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

    function fetchFilteredData(page = 1) {
        let month_year = $('#month_year').val();
        let department_id = $('#department_id').val();
        let follow_up_department_id = $('#follow_up_department_id').val();

        $.ajax({
            url: "{{ route('monthly-forms-report.filter') }}?page=" + page,
            method: "GET",
            data: {
                month_year: month_year,
                department_id: department_id,
                follow_up_department_id: follow_up_department_id,
            },
            success: function(response) {
                $('#donorsWithFormsTable').html(response.donorsWithFormsTable);
                $('.pagination-links').html(response.donorsPaginationLinks);
            }
        });
    }

    // Handle filter button click
    $('#filterButton').on('click', function() {
        fetchFilteredData(1);
    });

    // Handle pagination clicks dynamically
    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchFilteredData(page);
    });
});
</script>
@endpush