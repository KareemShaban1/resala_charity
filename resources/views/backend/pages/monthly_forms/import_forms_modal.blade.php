<x-modal id="importMonthlyFormModal" title="{{__('Import Monthly Forms')}}">
    <form id="importMonthlyFormForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="donorFile" class="form-label">{{__('Upload Excel File')}}</label>
                <input type="file" class="form-control" id="donorFile" name="file" accept=".xlsx,.csv" required>
                <div id="fileError" class="text-danger mt-2" style="display: none;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-success">{{__('Import')}}</button>
        </div>
    </form>
    <div id="feedbackMessage" class="alert mt-3" style="display: none;"></div>
</x-modal>