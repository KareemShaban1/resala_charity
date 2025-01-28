@extends('backend.layouts.master')
@section('title')
{{__('Collecting Lines and Donations')}}
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <!-- Add Collecting Line Button -->
                <div class="page-title-right">
                   
                </div>
                <h4 class="page-title">{{__('Collecting Lines and Donations')}}</h4>
            </div>
        </div>
    </div>


    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="date">{{__('Donation Date')}}:</label>
            <input type="date" id="date" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="area_group">{{__('Area Group')}}</label>
            <select id="area_group" class="form-control">
                @foreach($areaGroups as $group)
                <option value="" readonly>{{__('Select Area Group')}}</option>
                <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button id="filter-btn" class="btn btn-primary mt-4">{{__('Filter')}}</button>
        </div>
    </div>

    <!-- Collecting Lines Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h3>{{__('Collecting Lines')}}</h3>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCollectingLineModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Collecting Line')}}
                    </button>
        </div>
        <div class="card-body">
            <table id="collecting-lines-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Area Group')}}</th>
                        <th>{{__('Representative')}}</th>
                        <th>{{__('Driver')}}</th>
                        <th>{{__('Employee')}}</th>
                        <th>{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Donations Table -->
    <div class="card">
        <div class="card-header">
            <h3>{{__('Donations')}}</h3>
        </div>
        <div class="card-body">
            <table id="donations-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Donor Name')}}</th>
                        <th>{{__('Area')}}</th>
                        <th>{{__('Phones')}}</th>
                        <th>{{__('Monthly Form Day')}}</th>
                        <th>{{__('Collected')}}</th>
                        <th>{{__('Donates')}}</th>
                        <th>{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Forms Table -->
    <div class="card">
        <div class="card-header">
            <h3>{{__('Monthly Forms')}}</h3>
        </div>
        <div class="card-body">
            <table id="monthly-forms-table" class="table dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Donor Name')}}</th>
                        <th>{{__('Area')}}</th>
                        <th>{{__('Phones')}}</th>
                        <th>{{__('Collecting Donation Way')}}</th>
                        <th>{{__('Monthly Form Day')}}</th>
                        <th>{{__('Donates')}}</th>
                        <th>{{__('Actions')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Collecting Line Modal -->
@include('backend.pages.collecting-lines.addCollectingLineModal')

<!-- Edit Collecting Line Modal -->
@include('backend.pages.collecting-lines.editCollectingLineModal')

<!-- Delete Collecting Line Modal -->
@include('backend.pages.collecting-lines.deleteCollectingLineModal')

<!-- Select Collecting Line Modal -->
@include('backend.pages.collecting-lines.selectCollectingLineModal')

<!-- View Donations Modal -->
@include('backend.pages.collecting-lines.viewDonationsModal')

@include('backend.pages.collecting-lines.addMonthlyFormDonationModal')

@include('backend.pages.collecting-lines.assignDonationCollectingLineModal')

<!-- Edit Donation Modal -->
@include('backend.pages.donations.edit_modal')

<!-- Donation Details Modal -->
@include('backend.pages.donations.details_modal')

@include('backend.pages.donors.add_activity_modal')


<script>
    var donationCategories = @json($donationCategories);
</script>

@endsection

@include('backend.pages.collecting-lines.scripts')

@include('backend.pages.collecting-lines.scripts.edit_donation_scripts')
