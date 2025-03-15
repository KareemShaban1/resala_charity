<div class="row mb-3">
                <div class="col-md-3">
                    <label for="date-filter" class="form-label">{{__('Date Filter')}}</label>
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
                <div class="col-md-3">
                    <label for="donation-category-filter" class="form-label">{{__('Donation Category')}}</label>
                    <select id="donation-category-filter" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        <option value="normal">{{__('Normal Donation')}}</option>
                        <option value="monthly">{{__('Monthly Donation')}}</option>
                        <option value="gathered">{{__('Gathered Donation')}}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">{{__('Status')}}</label>
                    <select id="status-filter" class="form-control">
                        <option value="all">{{__('All')}}</option>
                        <option value="collected">{{__('Collected')}}</option>
                        <option value="not_collected">{{__('Not Collected')}}</option>
                        <option value="followed_up">{{__('Followed Up')}}</option>
                        <option value="cancelled">{{__('Cancelled')}}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="clear-filters" class="btn btn-secondary mt-4">{{__('Clear Filters')}}</button>
                </div>
            </div>