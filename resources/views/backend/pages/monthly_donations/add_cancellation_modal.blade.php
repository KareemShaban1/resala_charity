<x-modal id="addMonthlyDonationCancellationModal" title="{{__('Add Monthly Donation Cancellation')}}" size="lg">
    <form id="addMonthlyDonationCancellationForm" method="POST" action="{{ route('monthly-donations-cancellations.store') }}">
        @csrf
        <input type="hidden" name="monthly_donation_id" id="monthly_donation_id">
        <div class="modal-body">
            <div class="mb-3">
                <label for="reason" class="form-label">{{__('Reason')}}</label>
                <textarea name="reason" id="reason" class="form-control"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">{{__('Date')}}</label>
                <input type="datetime-local" class="form-control" id="date" name="date">
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>