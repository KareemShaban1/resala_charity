<x-modal id="editDonorModal" title="{{__('Edit Donor')}}" size="lg">
    <form id="editDonorForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_governorate_id" class="form-label">{{__('Governorate')}}</label>
                        <select class="form-control select2" id="edit_governorate_id" name="governorate_id" required>
                            <option value="">{{__('Select Governorate')}}</option>
                            @foreach(\App\Models\Governorate::all() as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_city_id" class="form-label">{{__('City')}}</label>
                        <select class="form-control select2" id="edit_city_id" name="city_id" required>
                            <option value="">{{__('Select City')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_area_id" class="form-label">{{__('Area')}}</label>
                        <select class="form-control select2" id="edit_area_id" name="area_id">
                            <option value="">{{__('Select Area')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="edit_street" class="form-label">{{__('Street')}}</label>
                        <input type="text" class="form-control" id="edit_street" name="street">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="edit_address" class="form-label">{{__('Address')}}</label>
                <input type="text" class="form-control" id="edit_address" name="address">
                <div class="invalid-feedback"></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_donor_type" class="form-label">{{__('Donor Type')}}</label>
                        <select class="form-select" id="edit_donor_type" name="donor_type">
                            <option value="normal">{{__('Normal')}}</option>
                            <option value="monthly">{{__('Monthly')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_active" class="form-label">{{__('Status')}}</label>
                        <select class="form-select" id="edit_active" name="active">
                            <option value="1">{{__('Active')}}</option>
                            <option value="0">{{__('Inactive')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="edit_monthly_donation_day" class="form-label">{{__('Monthly Donation Day')}}</label>
                        <input type="number" class="form-control" id="edit_monthly_donation_day" name="monthly_donation_day">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{__('Phone Numbers')}}</label>
                        <div id="edit-phone-container">
                            <!-- Phone inputs will be added here -->
                        </div>
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