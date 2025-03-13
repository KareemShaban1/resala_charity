<x-modal id="viewDonationsModal" title="{{__('View Donations')}}" size="xl">
    <div class="modal-body">
        <table id="view-donations-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('ID')}}</th>
                    <th>{{__('Donor Name')}}</th>
                    <th>{{__('Area')}}</th>
                    <th>{{__('Phones')}}</th>
                    <th>{{__('Monthly Form Day')}}</th>
                    <th>{{__('Collected')}}</th>
                    <th>{{__('Donates')}}</th>
                    <th>{{__('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated via DataTables -->
            </tbody>
        </table>
    </div>
</x-modal>