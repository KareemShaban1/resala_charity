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
            <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_monthly_donation_status" class="form-label">{{__('Status')}}</label>
                        <select class="form-control" name="status" id="edit_monthly_donation_status">
                            <option value="ongoing">{{__('Ongoing')}}</option>
                            <option value="cancelled">{{__('Cancelled')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="mb-3" id="edit-reason-container" style="display: none;">
                    <label for="reason" class="form-label">{{__('Reason')}}</label>
                    <textarea name="cancellation_reason" id="edit_cancellation_reason" class="form-control"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3" id="edit-date-container" style="display: none;">
                    <label for="date" class="form-label">{{__('Date')}}</label>
                    <input type="datetime-local" class="form-control" id="edit_cancellation_date" name="cancellation_date">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

         
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>