@extends('backend.layouts.master')

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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonthlyDonationModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Monthly Donation')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Monthly Donations')}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="monthly-donations-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Donor Name')}}</th>
                                <th>{{__('Area')}}</th>
                                <th>{{__('Address')}}</th>
                                <th>{{__('Phones')}}</th>
                                <th>{{__('Collecting Donation Way')}}</th>
                                <th>{{__('Monthly Donation Day')}}</th>
                                <!-- <th>{{__('Created At')}}</th> -->
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


@include('backend.pages.monthly_donations.add_cancellation_modal')

<!-- Add Monthly Donation Modal -->
@include('backend.pages.monthly_donations.add_modal')


<!-- Edit Monthly Donation Modal -->
@include('backend.pages.monthly_donations.edit_modal')

<script>
    var donationCategories = @json($donationCategories);
</script>
@endsection

@include('backend.pages.monthly_donations.scripts')