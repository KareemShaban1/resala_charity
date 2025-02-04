<x-modal id="editActivityModal" title="{{__('Edit Activity')}}" size="lg">
    <form id="editActivityForm" method="POST" >
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <input type="hidden" name="donor_id" id="edit_donor_id">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="activity_type" class="form-label">{{__('Activity Type')}}</label>
                        <select name="activity_type" id="edit_activity_type" class="form-control">
                            <option value="">{{__('Select Activity Type')}}</option>
                            <option value="call">{{__('Call')}}</option>
                            <option value="whatsapp_chat">{{__('WhatsApp Chat')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="call_type_id" class="form-label">{{__('Call Type')}}</label>
                        <select name="call_type_id" id="edit_call_type_id" class="form-control">
                            <option value="">{{__('Select Call Type')}}</option>
                            @foreach(\App\Models\CallType::all() as $callType)
                            <option value="{{$callType->id}}">{{$callType->name}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_time" class="form-label">{{__('Date Time')}}</label>
                        <input type="datetime-local" class="form-control" id="edit_date_time" name="date_time" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6" id="status-container" style="display: none;">
                    <div class="mb-3">
                        <label for="activity_status" class="form-label">{{__('Status')}}</label>
                        <select name="status" id="edit_activity_status" class="form-control">
                            <option value="" disabled>{{__('Select Status')}}</option>
                            <option value="ReplyAndDonate">{{__('Reply And Donate')}}</option>
                            <option value="ReplyAndNotDonate">{{__('Reply And Not Donate')}}</option>
                            <option value="NoReply">{{__('No Reply')}}</option>
                            <option value="PhoneNotAvailable">{{__('Phone Not Available')}}</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="response" class="form-label">{{__('Response')}}</label>
                <textarea name="response" id="edit_response" class="form-control"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">{{__('Notes')}}</label>
                <textarea name="notes" id="edit_notes" class="form-control"></textarea>
                <div class="invalid-feedback"></div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
        </div>
    </form>
</x-modal>