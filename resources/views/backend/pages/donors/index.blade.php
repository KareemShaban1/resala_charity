@extends('backend.layouts.master')

@section('title')
{{__('Donors')}}
@endsection

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addDonorModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Donor')}}
                    </button>
                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importDonorModal">
                        <i class="mdi mdi-upload"></i> {{__('Import Donors')}}
                    </button>

                    <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="mdi mdi-phone"></i> {{__('Check Phones')}}
                    </button>
                    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Donation')}}
                    </button> -->
                </div>
                <h4 class="page-title">{{__('Donors')}}</h4>

                <!-- Filters -->
                @include('backend.pages.donors.partials.filters')
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div style="width: 100%; overflow-x: auto;">

                        <table id="donors-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 30px;">{{__('Name')}}</th>
                                    <th>{{__('Donor Type')}}</th>
                                    <th>{{__('Donor Category')}}</th>
                                    <th>{{__('City')}}</th>
                                    <th>{{__('Area')}}</th>
                                    <th>{{__('Phones')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Actions')}}</th>
                                </tr>
                                <tr>
                                    <th>
                                        <!-- <input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By ID')}}"> -->
                                    </th>
                                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Name')}}"></th>
                                    <th>
                                        <select class="form-control form-control-sm column-search">
                                            <option value="">{{__('All')}}</option>
                                            <option value="monthly">{{__('Monthly')}}</option>
                                            <option value="normal">{{__('Normal')}}</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="form-control form-control-sm column-search">
                                            <option value="">{{__('All')}}</option>
                                            <option value="special">{{__('Special')}}</option>
                                            <option value="normal">{{__('Normal')}}</option>
                                        </select>
                                    </th>
                                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By City')}}"></th>
                                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Area')}}"></th>
                                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Phones')}}"></th>
                                    <th>
                                        <select class="form-control form-control-sm column-search">
                                            <option value="">{{__('All')}}</option>
                                            <option value="1">{{__('Active')}}</option>
                                            <option value="0">{{__('Inactive')}}</option>
                                        </select>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Donor Modal -->
@include('backend.pages.donors.add_modal')

<!-- Edit Donor Modal -->
@include('backend.pages.donors.edit_modal')

<!-- Import Donor Modal -->
@include('backend.pages.donors.import_modal')

@include('backend.pages.donors.assign_donors_modal')

@include('backend.pages.donors.add_activity_modal')

@include('backend.pages.donors.check_phones_modal')

@include('backend.pages.donors.donor_details_modal')

<!-- Add Donation Modal -->
@include('backend.pages.donations.add_modal')


@endsection

@include('backend.pages.donors.donor_js_code')