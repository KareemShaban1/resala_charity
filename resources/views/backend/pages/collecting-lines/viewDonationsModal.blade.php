<x-modal id="viewDonationsModal" title="{{__('View Donations')}}" size="xl">
<div class="row mb-3">

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
               
            </div>
    <div class="modal-body">
        <table id="view-donations-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('ID')}}</th>
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>{{__('Donor Name')}}</th>
                    <th>{{__('Area')}}</th>
                    <th>{{__('Phones')}}</th>
                    <th>{{__('Monthly Form Day')}}</th>
                    <th>{{__('Collected')}}</th>
                    <th>{{__('Donates')}}</th>
                    <th class="d-none">{{__('Collecting Line ID')}}</th>
                    <th>{{__('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated via DataTables -->
            </tbody>
        </table>
    </div>
</x-modal>