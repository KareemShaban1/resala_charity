@extends('backend.layouts.master')
@section('title')
{{__('Donations')}}
@endsection
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Donation')}}
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGatheredDonationModal">
                        <i class="mdi mdi-plus"></i> {{__('Gathered Donation')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Donations')}}</h4>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="date-filter">{{__('Date Filter')}}</label>
                    <select id="date-filter" class="form-control">
                        <option value="all">{{__('All Dates')}}</option>
                        <option value="today">{{__('Today')}}</option>
                        <option value="week">{{__('This Week')}}</option>
                        <option value="month">{{__('This Month')}}</option>
                        <option value="range">{{__('Custom Range')}}</option>
                    </select>
                </div>
                <div class="col-md-3" id="custom-range" style="display: none;">
                    <label for="start-date">{{__('Start Date')}}</label>
                    <input type="date" id="start-date" class="form-control">
                </div>
                <div class="col-md-3" id="end-date-container" style="display: none;">
                    <label for="end-date">{{__('End Date')}}</label>
                    <input type="date" id="end-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="donation-category-filter">{{__('Donation Category')}}</label>
                    <select id="donation-category-filter" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        <option value="normal">{{__('Normal Donation')}}</option>
                        <option value="monthly">{{__('Monthly Donation')}}</option>
                        <option value="gathered">{{__('Gathered Donation')}}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="clear-filters" class="btn btn-secondary mt-4">{{__('Clear Filters')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="donations-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Donor Name')}}</th>
                                <th>{{__('Area')}}</th>
                                <th>{{__('Phones')}}</th>
                                <th>{{__('Donation Type')}}</th>
                                <th>{{__('Donation Status')}}</th>
                                <th>{{__('Donates')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add Donation Modal -->
@include('backend.pages.donations.add_modal')


<!-- Edit Donation Modal -->
@include('backend.pages.donations.edit_modal')

<!-- Donation Details Modal -->
@include('backend.pages.donations.details_modal')

<!-- Gathered Donations Modal -->
@include('backend.pages.donations.gathered_donations_modal')

<script>
    var donationCategories = @json($donationCategories);
</script>

@endsection



@include('backend.pages.donations.scripts')