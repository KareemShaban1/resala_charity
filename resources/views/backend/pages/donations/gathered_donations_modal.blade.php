<x-modal id="addGatheredDonationModal" title="{{__('Add Gathered Donation')}}" size="lg">
    <form id="addGatheredDonationForm" method="POST">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="donation_category" value="gathered">
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
                        <label for="donation_status" class="form-label">{{__('Donation Status')}}</label>
                        <select class="form-control" name="status" id="donation_status">
                            <option value="collected">{{__('Collected')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>

            <div class="row">


                 <div class="col-md-4">
                    <div class="mb-3">
                        <label for="number_of_months" class="form-label">{{__('Number of Months')}}</label>
                        <input type="number" class="form-control" id="number_of_months" name="number_of_months">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="financial_receipt_number" class="form-label">{{__('Financial Receipt Number')}}</label>
                        <input type="text" class="form-control" id="financial_receipt_number" name="financial_receipt_number">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="donation_type" class="form-label">{{__('Donation Type')}}</label>
                        <select class="form-control" name="donation_type" id="donation_type" onchange="toggleDonationType()">
                            <option value="financial">{{__('Financial')}}</option>
                            <!-- <option value="inKind">{{__('In-Kind')}}</option>
                            <option value="both">{{__('Both')}}</option> -->
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="reporting_way" class="form-label">{{__('Activity Way')}}</label>
                        <select class="form-control" name="reporting_way" id="reporting_way">
                            <option value="call">{{__('Call')}}</option>
                            <option value="whatsapp_chat">{{__('Whatsapp Chat')}}</option>
                            <option value="other">{{__('Other')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="collecting_time" class="form-label">{{__('Collecting Time')}}</label>
                        <textarea name="collecting_time" id="collecting_time" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{__('Notes')}}</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                </div>


            </div>

            <!-- Financial Donations Section -->
            <div class="card" id="financial-donations-section">
                <div class="card-header">
                    <h4>{{__('Financial Donations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="gathered-financial-donation-rows-container">

                        <div class="row donation-row">
                            <input type="hidden" name="donates[0][financial_donation_type]" value="financial">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{__('Amount')}}</label>
                                    <input type="number" class="form-control amount" name="donates[0][financial_amount]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                           
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="collecting_date" class="form-label">{{__('Collecting Date')}}</label>
                                    <input type="date" class="form-control" id="collecting_date" name="donates[0][collecting_date]">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <!-- <div class="col-md-3 d-flex align-items-center">
                                <button type="button" class="btn btn-secondary mt-2 add-row-btn" data-target="#financial-donation-rows-container">{{__('Add Row')}}</button>
                            </div> -->
                        </div>
                    </div>

                </div>
            </div>

         

            <div class="row" id="collecting-section">
               
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="collecting_way" class="form-label">{{__('Collecting Way')}}</label>
                        <select name="collecting_way" id="collecting_way" class="form-control">
                            <option value="representative">{{__('Representative')}}</option>
                            <option value="location">{{__('Location')}}</option>
                            <option value="online">{{__('Online')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Collecting By')}}</label>
                        <select class="form-control select2" id="employee_id" name="employee_id">
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