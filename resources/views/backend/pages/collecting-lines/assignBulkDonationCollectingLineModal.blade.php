<!-- Assign Donation Modal -->
<div class="modal fade" id="assignBulkDonationModal" tabindex="-1" aria-labelledby="assignDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignBulkDonationModalLabel">{{__('Assign Donation to Collecting Line')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignDonationForm">
                    @csrf
                    <input type="hidden" name="donation_id" id="donation_id">
                    <div class="mb-3">
                        <label for="collecting_line_id" class="form-label">{{__('Select Collecting Line')}}</label>
                        <select class="form-control" id="bulk_collecting_line_id" name="collecting_line_id" required>
                            <!-- Collecting lines will be populated here dynamically -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                <button type="button" class="btn btn-primary" id="assignBulkDonationSubmit">{{__('Assign')}}</button>
            </div>
        </div>
    </div>
</div>