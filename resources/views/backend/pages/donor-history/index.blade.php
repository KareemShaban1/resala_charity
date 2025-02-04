@extends('backend.layouts.master')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>{{ __('Donor History') }}: {{ $donor->name }}</h2>
        </div>

        <div class="card-body">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="donorTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="donations-tab" data-bs-toggle="tab" data-bs-target="#donations" type="button" role="tab" aria-controls="donations" aria-selected="true">
                        {{ __('Donations') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="monthly-forms-tab" data-bs-toggle="tab" data-bs-target="#monthly-forms" type="button" role="tab" aria-controls="monthly-forms" aria-selected="false">
                        {{ __('Monthly Forms') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab" aria-controls="activities" aria-selected="false">
                        {{ __('Activities') }}
                    </button>
                </li>
            </ul>

            <!-- Date Filters -->
            <div class="mt-3 row" sttyle="display: flex; align-items: center;">
                <div class="col-md-3">
                    <label for="start-date" class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" class="form-control" id="start-date">
                </div>
                <div class="col-md-3">
                    <label for="end-date" class="form-label">{{ __('End Date') }}</label>
                    <input type="date" class="form-control" id="end-date">
                </div>
                <div class="col-md-3 mt-3">
                    <button type="button" class="btn btn-primary" id="filter-button">{{ __('Filter') }}</button>
                    <button type="button" class="btn btn-danger" id="clear-button">{{ __('Clear') }}</button>
                </div>

            </div>

            <!-- Tabs Content -->
            <div class="tab-content mt-3" id="donorTabsContent">
                <!-- Donations Tab -->
                <div class="tab-pane fade show active" id="donations" role="tabpanel" aria-labelledby="donations-tab">
                    <div id="donations-table-container">
                        <!-- AJAX-loaded donations table will be inserted here -->
                    </div>
                </div>

                <!-- Monthly Forms Tab -->
                <div class="tab-pane fade" id="monthly-forms" role="tabpanel" aria-labelledby="monthly-forms-tab">
                    <div id="monthly-forms-table-container">
                        <!-- AJAX-loaded monthly forms table will be inserted here -->
                    </div>
                </div>

                 <!-- Monthly Forms Tab -->
                 <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                    <div id="activities-table-container">
                        <!-- AJAX-loaded monthly forms table will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('backend.pages.donor-history.activities.activity_details_modal')
@include('backend.pages.donor-history.activities.edit_activity_modal')


@push('scripts')
<script>
    $(document).ready(function() {
        // Function to fetch data with optional date filters
        function fetchData(tab, startDate = '', endDate = '') {
            let url;
            switch (tab) {
                case 'donations':
                    $('#donations-tab').addClass('active');
                    $('#monthly-forms-tab').removeClass('active');
                    $('#activities-tab').removeClass('active');
                    url = `{{ url('donor-history') }}/{{ $donor->id }}/donations`;
                    break;
                case 'monthly-forms':
                    $('#monthly-forms-tab').addClass('active');
                    $('#donations-tab').removeClass('active');
                    $('#activities-tab').removeClass('active');
                    url = `{{ url('donor-history') }}/{{ $donor->id }}/monthly-forms`;
                    break;
                case 'activities':
                    $('#activities-tab').addClass('active');
                    $('#donations-tab').removeClass('active');
                    $('#monthly-forms-tab').removeClass('active');
                    url = `{{ url('donor-history') }}/{{ $donor->id }}/activities`;
                    break;    
                default:
                    $('#donations-tab').addClass('active');
                    $('#monthly-forms-tab').removeClass('active');
                    $('#activities-tab').removeClass('active');
                    url = `{{ url('donor-history') }}/{{ $donor->id }}/donations`;
                    break;
            }
           

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    if (tab === 'donations') {
                        $('#donations-table-container').html(response);
                    } else if(tab === 'monthly-forms') {
                        $('#monthly-forms-table-container').html(response);
                    }else if(tab === 'activities') {
                        $('#activities-table-container').html(response);
                    }
                },
                error: function() {
                    alert('{{ __("Failed to load data. Please try again.") }}');
                }
            });
        }

        // Load donations by default on page load
        fetchData('donations');

        // Handle tab switching
        $('#donorTabs button').on('click', function() {
            // let tab = $(this).attr('id') === 'donations-tab' ? 'donations' : 'monthly-forms';
            let tab;
            switch ($(this).attr('id')) {
                case 'donations-tab':
                    tab = 'donations';
                    break;
                case 'monthly-forms-tab':
                    tab = 'monthly-forms';
                    break;
                case 'activities-tab':
                    tab = 'activities';
                    break;          
            }
            fetchData(tab);
        });

        // Handle date filter button click
        $('#filter-button').on('click', function() {
            let startDate = $('#start-date').val();
            let endDate = $('#end-date').val();
            // let activeTab = $('#donorTabs .nav-link.active').attr('id') === 'donations-tab' ? 'donations' : 'monthly-forms';

            let activeTab;
            switch ($('#donorTabs .nav-link.active').attr('id')) {
                case 'donations-tab':
                    activeTab = 'donations';
                    break;
                case 'monthly-forms-tab':
                    activeTab = 'monthly-forms';
                    break;
                case 'activities-tab':
                    activeTab = 'activities';
                    break;          
            }
            console.log($('#donorTabs .nav-link.active').attr('id'))
            fetchData(activeTab, startDate, endDate);
        });
        $('#clear-button').on('click', function() {

            // Clear input values
            $('#start-date').val('');
            $('#end-date').val('');

            // Get updated start and end date values (which are now empty)
            let startDate = $('#start-date').val();
            let endDate = $('#end-date').val();

            // Determine the active tab
            // let activeTab = $('#donorTabs .nav-link.active').attr('id') === 'donations-tab' ? 'donations' : 'monthly-forms';

            let activeTab;
            switch ($('#donorTabs .nav-link.active').attr('id')) {
                case 'donations-tab':
                    activeTab = 'donations';
                    break;
                case 'monthly-forms-tab':
                    activeTab = 'monthly-forms';
                    break;
                case 'activities-tab':
                    activeTab = 'activities';
                    break;          
            }
            // Fetch data based on cleared filters
            fetchData(activeTab, startDate, endDate);
        });




    });
</script>
@endpush
@endsection