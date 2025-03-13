<x-modal id="addAreaModal" title="{{__('Add New Area')}}">
    <form id="addAreaForm" method="POST" action="{{ route('areas.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="governorate_id" class="form-label">{{__('Governorate')}}</label>
                <select class="form-control select2" id="governorate_id" required>
                    <option value="">{{__('Select Governorate')}}</option>
                    @foreach(\App\Models\Governorate::all() as $governorate)
                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="city_id" class="form-label">{{__('City')}}</label>
                <select class="form-control select2" id="city_id" name="city_id" required>
                    <option value=""> {{__('Select City')}}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="area_group_id" class="form-label">{{__('Area Group')}}</label>
                <select class="form-control select2" id="area_group_id" name="area_group_id">
                    <option value="">{{__('Select Area Group')}}</option>
                    @foreach(\App\Models\AreaGroup::all() as $area_group)
                    <option value="{{ $area_group->id }}">{{ $area_group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>