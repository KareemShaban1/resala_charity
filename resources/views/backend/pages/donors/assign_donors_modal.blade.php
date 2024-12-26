<x-modal id="assignDonorModal" title="{{__('Assign Donors')}}" size="lg">
    <form id="assignDonorForm" method="POST" action="{{ route('donors.assign') }}">
        @csrf
        <input type="hidden" name="parent_donor_id" id="parent_donor_id">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                <div class="mb-3">
                <label for="assign_donor_id" class="form-label">{{__('Child Donor')}}</label>
                <select class="form-control select2" name="donor_id" id="assign_donor_id">
                    <option value="">{{__('Select Child Donor')}}</option>
                </select>
            </div>
                </div>
            </div>
            <hr>
            <div>
                <h5>{{__('Children Donors')}}</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{__('ID')}}</th>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Address')}}</th>
                        </tr>
                    </thead>
                    <tbody id="childrenDonorTableBody">
                        <!-- Dynamic content will be added here -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-success">{{__('Assign')}}</button>
        </div>
    </form>
    <div id="feedbackMessage" class="alert mt-3" style="display: none;"></div>
</x-modal>