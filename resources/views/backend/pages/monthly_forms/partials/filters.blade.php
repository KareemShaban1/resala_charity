<div class="row mb-3">
                <div class="col-md-3">
                    <label for="date-filter" class="form-label">{{__('Date')}}</label>
                    <select id="date-filter" class="form-control">
                        <option value="all">{{__('All Dates')}}</option>
                        <option value="today">{{__('Today')}}</option>
                        <option value="week">{{__('This Week')}}</option>
                        <option value="month">{{__('This Month')}}</option>
                        <option value="range">{{__('Custom Range')}}</option>
                    </select>
                </div>
                <div class="col-md-3" id="custom-range" style="display: none;">
                    <label for="start-date">{{__('Start Date')}}</label>
                    <input type="date" id="start-date" class="form-control">
                </div>
                <div class="col-md-3" id="end-date-container" style="display: none;">
                    <label for="end-date">{{__('End Date')}}</label>
                    <input type="date" id="end-date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="department-filter" class="form-label">{{__('Department')}}</label>
                    <select id="department-filter" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        @foreach (App\Models\Department::all() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="employee-filter" class="form-label">{{__('Employee')}}</label>
                    <select id="employee-filter" class="form-control">
                        
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="follow-up-department-filter" class="form-label">{{__('Follow Up Department')}}</label>
                    <select id="follow-up-department-filter" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        @foreach (App\Models\Department::all() as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button id="clear-filters" class="btn btn-secondary mt-4">{{__('Clear Filters')}}</button>
                </div>
            </div>