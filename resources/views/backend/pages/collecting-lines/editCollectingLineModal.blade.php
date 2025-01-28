<x-modal id="editCollectingLineModal" title="{{__('Edit Collecting Line')}}" size="lg">

    <form id="editCollectingLineForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_representative_id">{{__('Representative')}}</label>
                        <select name="representative_id" id="edit_representative_id" class="form-control">
                            @foreach($representatives as $representative)
                            <option value="{{ $representative->id }}">{{ $representative->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_driver_id">{{__('Driver')}}</label>
                        <select name="driver_id" id="edit_driver_id" class="form-control">
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_employee_id">{{__('Employee')}}</label>
                        <select name="employee_id" id="edit_employee_id" class="form-control">
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_area_group_id">{{__('Grand Area')}}</label>
                        <select name="area_group_id" id="edit_area_group_id" class="form-control">
                            @foreach($areaGroups as $areaGroup)
                            <option value="{{ $areaGroup->id }}">{{ $areaGroup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_collecting_date">{{__(key: 'Collecting Date')}}</label>
                        <input type="date" class="form-control" id="edit_collecting_date" name="collecting_date" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>

        </div>
    </form>
    </div>

</x-modal>