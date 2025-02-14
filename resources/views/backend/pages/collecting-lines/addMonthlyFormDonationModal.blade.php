<x-modal id="addMonthlyFormDonationModal" title="{{__('Add Monthly Form Donation')}}" size="lg">

    <form id="addMonthlyFormDonationForm">
        @csrf
        <!-- <input type="hidden" name="donation_category" id="add_donation_category" value="monthly"> -->
        <input type="hidden" name="monthly_form_id" id="add_monthly_form_id">

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="add_donor_id" class="form-label">{{__('Donor Name')}}</label>
                        <select class="form-control select2" id="add_donor_id" name="donor_id" required>
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
                        <label for="add_donation_status" class="form-label">{{__('Status')}}</label>
                        <select class="form-control" name="status" id="add_donation_status" onchange="toggleEditDonationStatus()">
                            <option value="collected">{{__('Collected')}}</option>
                            <option value="not_collected">{{__('Not Collected')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="add_date" class="form-label">{{__('Donation Date')}}</label>
                        <input type="date" class="form-control" id="add_date" name="date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="add_donation_type" class="form-label">{{__('Donation Type')}}</label>
                        <select class="form-control" name="donation_type" id="add_donation_type" onchange="toggleAddDonationType()">
                            <option value="financial">{{__('Financial')}}</option>
                            <option value="inKind">{{__('In-Kind')}}</option>
                            <option value="both">{{__('Both')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="reporting_way" class="form-label">{{__('Activity Way')}}</label>
                        <select class="form-control" name="reporting_way" id="add_reporting_way">
                            <option value="call">{{__('Call')}}</option>
                            <option value="whatsapp_chat">{{__('WhatsApp Chat')}}</option>
                            <!-- <option value="monthly_donation" selected>{{__('Monthly Donation')}}</option> -->
                            <option value="other">{{__('Other')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="add_donation_category" class="form-label">{{__('Donation Type')}}</label>
                        <select class="form-control" name="donation_category" id="add_donation_category">
                        <option value="monthly">{{__('Monthly')}}</option>
                        <option value="normal">{{__('Normal')}}</option>
                            <option value="normal_and_monthly">{{__('Normal and Monthly')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="add_collecting_time" class="form-label">{{__('Collecting Time')}}</label>
                        <textarea name="collecting_time" id="add_collecting_time" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="add_notes" class="form-label">{{__('Notes')}}</label>
                        <textarea name="notes" id="add_notes" class="form-control"></textarea>
                    </div>
                </div>

            </div>

            <!-- Financial Donations Section -->
            <div class="card d-none" id="add-financial-donations-section">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{__('Financial Donations')}}</h4>
                        <button type="button" class="btn btn-secondary mt-2"
                            id="add-financial-row-edit"
                            data-target="#add-financial-donation-rows-container">{{__('Add Row')}}</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="add-financial-donation-rows-container">
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][financial_donation_type]" value="financial">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="financial_donation_item_type" class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control" name="donates[0][financial_donation_item_type]">
                                        <option value="normal">{{__('Normal')}}</option>
                                        <option value="monthly">{{__('Monthly')}}</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="donates[0][financial_amount]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="receipt_number" class="form-label">{{__('Financial Receipt Number')}}</label>
                                    <input type="text" class="form-control" name="donates[0][financial_receipt_number]">
                                    <div class="invalid-feedback"></div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- In-Kind Donations Section -->
            <div class="card mt-4 d-none" id="add-in-kind-donations-section">
                <div class="card-header">

                    <div class="d-flex align-items-center justify-content-between">
                        <h4>{{__('In-Kind Donations')}}</h4>
                        <button type="button" class="btn btn-secondary mt-2"
                            id="add-in-kind-row-edit"
                            data-target="#add-in-kind-donation-rows-container">{{__('Add Row')}}</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="add-in-kind-donation-rows-container">
                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][inKind_donation_type]" value="inKind">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Item Name')}}</label>
                                    <input type="text" class="form-control" name="donates[0][in_kind_item_name]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Quantity')}}</label>
                                    <input type="number" class="form-control" name="donates[0][in_kind_quantity]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="in_kind_donation_item_type" class="form-label">{{__('Donation Category')}}</label>
                                    <select class="form-control" name="donates[0][in_kind_donation_item_type]">
                                        <option value="normal">{{__('Normal')}}</option>
                                        <option value="monthly">{{__('Monthly')}}</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-none" id="add-collecting-section">
                <div class="col-md-6 d-none" id="add-in-kind-receipt-container">
                    <div class="mb-3">
                        <label for="receipt_number" class="form-label">{{__('In Kind Receipt Number')}}</label>
                        <input type="text" class="form-control" id="add_in_kind_receipt_number" name="in_kind_receipt_number">
                        <div class="invalid-feedback"></div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="add_collecting_date" class="form-label">{{__('Collecting Date')}}</label>
                        <input type="date" class="form-control" id="add_collecting_date" name="collecting_date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="add_collecting_way" class="form-label">{{__('Collecting Way')}}</label>
                        <select name="collecting_way" id="add_collecting_way">
                            <option value="representative">{{__('Representative')}}</option>
                            <option value="location">{{__('Location')}}</option>
                            <option value="online">{{__('Online')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="add_employee_id" class="form-label">{{__('Collecting By')}}</label>
                        <select class="form-control select2" id="add_employee_id" name="employee_id">
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
            <button type="submit" class="btn btn-primary">{{__('Add As Donation')}}</button>
        </div>
    </form>

</x-modal>