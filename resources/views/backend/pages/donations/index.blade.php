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
             @include('backend.pages.donations.partials.filters')
           
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

@include('backend.pages.donors.add_activity_modal')


<script>
    var donationCategories = @json($donationCategories);
</script>

@endsection



@include('backend.pages.donations.scripts')