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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonthlyFormModal">
                        <i class="mdi mdi-plus"></i> {{__('Add Monthly Form')}}
                    </button>
                </div>
                <h4 class="page-title">{{__('Monthly Forms')}}</h4>

                <!-- Filters -->
                @include('backend.pages.monthly_forms.partials.filters')
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="monthly-forms-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Donor Name')}}</th>
                                <th>{{__('Area')}}</th>
                                <th>{{__('Phones')}}</th>
                                <th>{{__('Monthly Form Day')}}</th>
                                <th>{{__('Donates')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                            <tr>
                                <th>
                                    <!-- <input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By ID')}}"> -->
                                </th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Donor Name')}}"></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Area')}}"></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Phones')}}"></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="{{__('Search By Monthly Form Day')}}"></th>
                                <th>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align:right">{{__('Total Financial Donations')}}</th>
                                <th id="financial-total" colspan="4"></th> <!-- Make sure this matches the column index -->
                                <!-- <th></th> Empty column for action buttons -->
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>



<!-- Add Monthly Form Modal -->
@include('backend.pages.monthly_forms.add_modal')


<!-- Edit Monthly Form Modal -->
@include('backend.pages.monthly_forms.edit_modal')


<!-- View Donations Modal -->
@include('backend.pages.monthly_forms.details_modal')

<script>
    var donationCategories = @json($donationCategories);
</script>
@endsection

@include('backend.pages.monthly_forms.scripts')