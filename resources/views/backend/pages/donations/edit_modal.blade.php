<x-modal id="editDonationModal" title="{{__('Edit Donation')}}" size="lg">
    <form id="editDonationForm" method="POST">
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
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_donation_status" class="form-label">{{__('Status')}}</label>
                        <select class="form-control" name="status" id="edit_donation_status" onchange="toggleDonationStatus()">
                            <option value="collected">{{__('Collected')}}</option>
                            <option value="not_collected">{{__('Not_Collected')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_date" class="form-label">{{__('Donation Date')}}</label>
                        <input type="date" class="form-control" id="edit_date" name="date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_donation_type" class="form-label">{{__('Donation Type')}}</label>
                        <select class="form-control" name="donation_type" id="edit_donation_type" onchange="toggleDonationType()">
                            <option value="Financial">{{__('Financial')}}</option>
                            <option value="inKind">{{__('In-Kind')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>


            </div>

            <!-- Financial Donations Section -->
            <div class="card d-none" id="edit-financial-donations-section">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{__('Financial Donations')}}</h4>
                        <button type="button" class="btn btn-secondary mt-2 add-row-btn" 
                        id="add-financial-row-edit"
                        data-target="#edit-financial-donation-rows-container">{{__('Add Row')}}</button>
                    </div>
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

                        </div>
                    </div>
                </div>
            </div>

            <!-- In-Kind Donations Section -->
            <div class="card mt-4 d-none" id="edit-in-kind-donations-section">
                <div class="card-header">

                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{__('In-Kind Donations')}}</h4>
                        <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#edit-in-kind-donation-rows-container">{{__('Add Row')}}</button>
                    </div>
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

                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-none" id="edit-collecting-section">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_receipt_number" class="form-label">{{__('Receipt Number')}}</label>
                        <input type="text" class="form-control" id="edit_receipt_number" name="receipt_number">
                        <div class="invalid-feedback"></div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_collecting_date" class="form-label">{{__('Collecting Date')}}</label>
                        <input type="date" class="form-control" id="edit_collecting_date" name="collecting_date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_employee_id" class="form-label">{{__('Collecting By')}}</label>
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