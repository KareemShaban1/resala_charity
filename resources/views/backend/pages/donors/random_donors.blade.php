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

                </div>
                <h4 class="page-title">{{__('Random Donors')}}</h4>

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

                        <table id="random-donors-table" class="table dt-responsive nowrap w-100">
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
                                    <th>{{__('Has Activities')}}</th>
                                    <th>{{__('Last Activity Status')}}</th>
                                    <th>{{__('Actions')}}</th>
                                </tr>
                                <tr>
                                    <th>
                                        <!-- <input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By ID')}}"> -->
                                    </th>
                                    <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Name')}}"></th>
                                    <th>
                                        <select class="form-control form-control-sm column-search">
                                            <option value="normal">{{__('Normal')}}</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="form-control form-control-sm column-search">
                                            <option value="random">{{__('Random')}}</option>
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
                                    <th>
                                    <select class="form-control form-control-sm column-search">
                                            <option value="">{{__('All')}}</option>
                                            <option value="yes">{{__('Yes')}}</option>
                                            <option value="no">{{__('No')}}</option>
                                        </select>
                                    </th>
                                   <th> <select class="form-control form-control-sm column-search">
                                            <option value="">{{__('All')}}</option>
                                            <option value="ReplyAndDonate">{{__('Reply And Donate')}}</option>
                                            <option value="ReplyAndNotDonate">{{__('Reply And Not Donate')}}</option>
                                            <option value="NoReply">{{__('No Reply')}}</option>
                                            <option value="PhoneNotAvailable">{{__('Phone Not Available')}}</option>
                                        </select></th>
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

@include('backend.pages.donors.add_activity_modal')

<!-- Edit Donor Modal -->
@include('backend.pages.donors.edit_modal')

@include('backend.pages.donors.donor_details_modal')


@endsection

@include('backend.pages.donors.donor_js_code')