<x-modal id="selectCollectingLineModal" title="{{__('Select Collecting Line')}}" size="lg">
    <div class="modal-body">
        <table id="select-collecting-lines-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('ID')}}</th>
                    <th>{{__('Area Group')}}</th>
                    <th>{{__('Representative')}}</th>
                    <th>{{__('Driver')}}</th>
                    <th>{{__('Employee')}}</th>
                    <th>{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated via DataTables -->
            </tbody>
        </table>
    </div>
</x-modal>