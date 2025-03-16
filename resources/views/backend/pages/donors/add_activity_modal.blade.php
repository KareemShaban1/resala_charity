<x-modal id="addActivityModal" title="{{__('Add Activity')}}" size="lg">
    <form id="addActivityForm" method="POST" action="{{ route('activities.store') }}">
        @csrf
        <input type="hidden" name="donor_id" id="add_activity_donor_id">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="activity_type" class="form-label">{{__('Activity Type')}}</label>
                        <select name="activity_type" id="activity_type" class="form-control">
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
                        <select name="call_type_id" id="call_type_id" class="form-control">
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
                        <input type="datetime-local" class="form-control" id="date_time" name="date_time" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="activity_status_id" class="form-label">{{__('Status')}}</label>
                        <select name="activity_status_id" id="activity_status_id" class="form-control">
                            <option value="" selected>{{__('Select Status')}}</option>
                            @foreach(\App\Models\ActivityStatus::active()->get() as $status)
                            <option value="{{$status->id}}">{{$status->name}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="activity_reason_id" class="form-label">{{__('Reason')}}</label>
                        <select name="activity_reason_id" id="activity_reason_id" class="form-control">
                            <option value="" selected>{{__('Select Reason')}}</option>
                            @foreach(\App\Models\ActivityReason::active()->get() as $reason)
                            <option value="{{$reason->id}}">{{$reason->name}}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="response" class="form-label">{{__('Response')}}</label>
                <textarea name="response" id="response" class="form-control"></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">{{__('Notes')}}</label>
                <textarea name="notes" id="notes" class="form-control"></textarea>
                <div class="invalid-feedback"></div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
        </div>
    </form>
</x-modal>