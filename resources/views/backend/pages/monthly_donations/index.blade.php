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

<!-- Add Monthly Donation Modal -->
<x-modal id="addMonthlyDonationModal" title="{{__('Add Monthly Donation')}}" size="lg">
    <form id="addMonthlyDonationForm" method="POST" action="{{ route('monthly-donations.store') }}">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Donor Name')}}</label>
                        <select class="form-control select2" id="donor_id" name="donor_id" required>
                            <option value="">{{__('Select Donor')}}</option>
                            @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Donations Section -->
            <div class="card">
                <div class="card-header">
                    <h4>{{__('Financial Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="financial-donation-rows-container">
                        <!-- Example Row -->
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][financial_donation_type]" value="Financial">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="donates[0][financial_donation_categories_id]">
                                        <option value="">{{__('Select Category')}}</option>
                                        @foreach($donationCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="donates[0][financial_amount]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#financial-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- In-Kind Donations Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>{{__('In-Kind Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="in-kind-donation-rows-container">
                        <!-- Rows for in-kind donations will be added here -->
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][inKind_donation_type]" value="inKind">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="donates[0][in_kind_item_name]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                                    <input type="number" class="form-control" name="donates[0][in_kind_quantity]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#in-kind-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="collecting_donation_way" class="form-label">{{__('Collecting Donation Way')}}</label>
                        <select class="form-control" name="collecting_donation_way" id="collecting_donation_way">
                            <option value="online">{{__('Online')}}</option>
                            <option value="location">{{__('Location')}}</option>
                            <option value="representative">{{__('Representative')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Department')}}</label>
                        <select class="form-control select2" id="department_id" name="department_id" required>
                            <option value="">{{__('Select Department')}}</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Employee')}}</label>
                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                            <option value="">{{__('Select Employee')}}</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>


<!-- Edit Monthly Donation Modal -->
<x-modal id="editMonthlyDonationModal" title="{{__('Edit Monthly Donation')}}" size="lg">
    <form id="editMonthlyDonationForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_donor_id" class="form-label">{{__('Donor Name')}}</label>
                        <select class="form-control select2" id="edit_donor_id" name="donor_id" required>
                            <option value="">{{__('Select Donor')}}</option>
                            @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Donations Section -->
            <div class="card">
                <div class="card-header">
                    <h4>{{__('Financial Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="edit-financial-donation-rows-container">
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][financial_donation_type]" value="Financial">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="donates[0][financial_donation_categories_id]">
                                        <option value="">{{__('Select Category')}}</option>
                                        @foreach($donationCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="donates[0][financial_amount]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#edit-financial-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- In-Kind Donations Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>{{__('In-Kind Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="edit-in-kind-donation-rows-container">
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][inKind_donation_type]" value="inKind">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="donates[0][in_kind_item_name]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Quantity')}}</label>
                                    <input type="number" class="form-control" name="donates[0][in_kind_quantity]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#in-kind-donation-rows-container">{{__('Add Row')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Collecting Donation Way -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_collecting_donation_way" class="form-label">{{__('Collecting Donation Way')}}</label>
                        <select class="form-control" name="collecting_donation_way" id="edit_collecting_donation_way">
                            <option value="online">{{__('Online')}}</option>
                            <option value="location">{{__('Location')}}</option>
                            <option value="representative">{{__('Representative')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Department -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_department_id" class="form-label">{{__('Department')}}</label>
                        <select class="form-control select2" id="edit_department_id" name="department_id" required>
                            <option value="">{{__('Select Department')}}</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Employee -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_employee_id" class="form-label">{{__('Employee')}}</label>
                        <select class="form-control select2" id="edit_employee_id" name="employee_id" required>
                            <option value="">{{__('Select Employee')}}</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>

<script>
    var donationCategories = @json($donationCategories);
</script>
@endsection

@include('backend.pages.monthly_donations.scripts')

