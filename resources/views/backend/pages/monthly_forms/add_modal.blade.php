<x-modal id="addMonthlyFormModal" title="{{__('Add Monthly Form')}}" size="lg">
    <form id="addMonthlyFormForm" method="POST" action="{{ route('monthly-forms.store') }}">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="monthly_donation_status" class="form-label">{{__('Status')}}</label>
                        <select class="form-control" name="status" id="monthly_donation_status">
                            <option value="ongoing">{{__('Ongoing')}}</option>
                            <option value="cancelled">{{__('Cancelled')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
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

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{__('Notes')}}</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="donation_type" class="form-label">{{__('Donation Type')}}</label>
                        <select class="form-control" name="donation_type" id="donation_type" onchange="toggleDonationType()">
                            <option value="financial">{{__('Financial')}}</option>
                            <option value="inKind">{{__('In-Kind')}}</option>
                            <option value="both">{{__('Both')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Donations Section -->
            <div class="card" id="financial-donations-section">
                <div class="card-header">
                    <h4>{{__('Financial Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="financial-donation-rows-container">
                        <!-- Example Row -->
                        <div class="row donation-row">
                            <input type="hidden" name="items[0][financial_donation_type]" value="financial">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="donation_category" class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control donation-category" name="items[0][financial_donation_categories_id]">
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
                                    <input type="number" class="form-control amount" name="items[0][financial_amount]">
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
            <div class="card mt-4 d-none" id="in-kind-donations-section">
                <div class="card-header">
                    <h4>{{__('In-Kind Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="in-kind-donation-rows-container">
                        <!-- Rows for in-kind donations will be added here -->
                        <div class="row donation-row">
                            <input type="hidden" name="items[0][inKind_donation_type]" value="inKind">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_name" class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="items[0][in_kind_item_name]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('Quantity')}}</label>
                                    <input type="number" class="form-control" name="items[0][in_kind_quantity]">
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
           

                <div class="mb-3" id="reason-container" style="display: none;">
                    <label for="reason" class="form-label">{{__('Reason')}}</label>
                    <textarea name="cancellation_reason" id="reason" class="form-control"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3" id="date-container" style="display: none;">
                    <label for="date" class="form-label">{{__('Date')}}</label>
                    <input type="datetime-local" class="form-control" id="date" name="cancellation_date">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>