<x-modal id="addCollectingLineModal" title="{{__('Add Collecting Line')}}" size="lg">

    <form id="addCollectingLineForm">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="name">{{__('Representative')}}</label>
                        <select name="representative_id" id="representative_id" class="form-control">
                            @foreach($representatives as $representative)
                            <option value="{{ $representative->id }}">{{ $representative->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="col-md-6 mb-3">

                    <div class="form-group">
                        <label for="driver_id">{{__('Driver')}}</label>
                        <select name="driver_id" id="driver_id" class="form-control">
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="employee_id">{{__('Employee')}}</label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="area_group_id">{{__('Area Group')}}</label>
                        <select name="area_group_id" id="area_group_id" class="form-control">
                            @foreach($areaGroups as $areaGroup)
                            <option value="{{ $areaGroup->id }}">{{ $areaGroup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="collecting_date">{{__(key: 'Collecting Date')}}</label>
                        <input type="date" class="form-control" id="collecting_date" name="collecting_date" required>
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