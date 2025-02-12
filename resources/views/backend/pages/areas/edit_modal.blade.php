<x-modal id="editAreaModal" title="{{__('Edit Area')}}">
    <form id="editAreaForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_name" class="form-label">{{__('Name')}}</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_governorate_id" class="form-label">{{__('Governorate')}}</label>
                <select class="form-control" id="edit_governorate_id" required>
                    <option value="">{{__('Select Governorate')}}</option>
                    @foreach(\App\Models\Governorate::all() as $governorate)
                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="edit_city_id" class="form-label">{{__('City')}}</label>
                <select class="form-control" id="edit_city_id" name="city_id" required>
                    <option value="">{{__('Select City')}}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="edit_area_group_id" class="form-label">{{__('Area Group')}}</label>
                <select class="form-control select2" id="edit_area_group_id" name="area_group_id">
                    <option value="">{{__('Select Area Group')}}</option>
                    @foreach(\App\Models\AreaGroup::all() as $area_group)
                    <option value="{{ $area_group->id }}">{{ $area_group->name }}</option>
                    @endforeach
                   
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>