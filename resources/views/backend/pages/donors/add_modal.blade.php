<x-modal id="addDonorModal" title="{{__('Add Donor')}}" size="lg">
    <form id="addDonorForm" method="POST" action="{{ route('donors.store') }}">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('Name')}}</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="governorate_id" class="form-label">{{__('Governorate')}}</label>
                        <select class="form-control select2" id="governorate_id" name="governorate_id">
                            <option value="">{{__('Select Governorate')}}</option>
                            @foreach(\App\Models\Governorate::all() as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="city_id" class="form-label">{{__('City')}}</label>
                        <select class="form-control select2" id="city_id" name="city_id">
                            <option value="">{{__('Select City')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">

                    <div class="mb-3">
                        <label for="area_id" class="form-label">{{__('Area')}}</label>
                        <select class="form-control select2" id="area_id" name="area_id">
                            <option value="">{{__('Select Area')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                </div>
            </div>

            <div class="row">


                <div class="col-md-6">

                    <div class="mb-3">
                        <label for="street" class="form-label">{{__('Street')}}</label>
                        <input type="text" class="form-control" id="street" name="street">
                        <div class="invalid-feedback"></div>
                    </div>

                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="department_id" class="form-label">{{__('Department')}}</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">{{__('Select Department') }}</option>
                            @foreach(\App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="donor_category" class="form-label">{{__('Donor Category')}}</label>
                        <select class="form-select" id="donor_category" name="donor_category">
                            <option value="normal">{{__('Normal')}}</option>
                            <option value="special">{{__('Special')}}</option>
                            <option value="random">{{__('Random')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">{{__('Address')}}</label>
                <input type="text" class="form-control" id="address" name="address">
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label for="notes" class="form-label">{{__('Notes')}}</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>
            </div>

           

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="donor_type" class="form-label">{{__('Donor Type')}}</label>
                        <select class="form-select" id="donor_type" name="donor_type">
                            <option value="normal">{{__('Normal')}}</option>
                            <option value="monthly">{{__('Monthly')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="active" class="form-label">{{__('Status')}}</label>
                        <select class="form-select" id="active" name="active">
                            <option value="1">{{__('Active')}}</option>
                            <option value="0">{{__('Inactive')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="monthly_donation_day" class="form-label">{{__('Monthly Form Day')}}</label>
                        <input type="number" class="form-control" name="monthly_donation_day">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{__('Phone Numbers')}}</label>
                        <div id="phone-container">
                            <div class="input-group mb-2">
                                <input type="text" name="phones[0][number]" class="form-control" placeholder="{{__('Enter phone number')}}">
                                <select name="phones[0][type]" class="form-select" style="max-width: 150px;">
                                    <option value="mobile">{{__('Mobile')}}</option>
                                    <option value="home">{{__('Home')}}</option>
                                    <option value="work">{{__('Work')}}</option>
                                    <option value="other">{{__('Other')}}</option>
                                </select>
                                <button type="button" class="btn btn-success add-phone"><i class="mdi mdi-plus"></i></button>
                            </div>
                        </div>
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