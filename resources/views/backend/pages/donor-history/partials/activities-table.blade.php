@if($activities->isEmpty())
<p>{{ __('No Activities found.') }}</p>
@else
<table id="activities-table" class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Activity Type') }}</th>
            <th>{{ __('Call Type') }}</th>
            <th>{{ __('Date Time') }}</th>
            <th>{{ __('Created By') }}</th>
            <th>{{ __('Actions') }}</th>


        </tr>
    </thead>
    <tbody>
        @foreach($activities as $activity)

        <tr>
            <td>{{ $activity->id }}</td>
            <td>{{ $activity->activity_type }}</td>
            <td>{{ $activity->callType->name }}</td>
            <td>{{ $activity->date_time }}</td>
            <td>{{ $activity->createdBy->name }}</td>
            <td>
                <button class="btn btn-danger" onclick="editActivity({{ $activity->id }})">{{ __('Edit') }}</button>

                <button class="btn btn-primary" onclick="showActivityDetails({{ $activity->id }})">
                    {{ __('Activity Details') }}</button>
            </td>

        </tr>
        @endforeach

    </tbody>
</table>

<script>
    function showActivityDetails(id) {
        $('#activityDetailsModal').modal('show');

        $.get(`{{ url('donor-history/activity') }}/${id}`)
            .done(function(data) {
                // Construct the content to be displayed in the modal
                let modalContent = `
                   <p><strong>{{ __('Donor') }}:</strong> ${data.donor?.name ?? 'N/A' }</p>
            <p><strong>{{ __('Activity Type') }}:</strong> ${data.activity_type}</p>
            <p><strong>{{ __('Call Type') }}:</strong> ${data.call_type?.name ?? 'N/A'}</p>
             <p><strong>{{ __('Status') }}:</strong> 
            ${data.status === "ReplyAndDonate" ? '{{__("Reply And Donate")}}' :
             data.status === "ReplyAndNotDonate" ? '{{__("Reply And Not Donate")}}' : 
             data.status === "NoReply" ? '{{__("No Reply")}}' :
             data.status === "PhoneNotAvailable" ? '{{__("Phone Not Available")}}' : ''}
            </p>
            <p><strong>{{ __('Date Time') }}:</strong> ${data.date_time }</p>
            <p><strong>{{ __('Created By') }}:</strong> ${data.created_by?.name ?? 'N/A' }</p>
            <p><strong>{{ __('Notes') }}:</strong> ${data.notes ?? 'N/A' }</p>
            <p><strong>{{ __('Response') }}:</strong> ${data.response ?? 'N/A'}</p>
          
        `;
                // Add the constructed content to the modal body
                $('#activityDetailsModal .modal-body').html(modalContent);

            })
            .fail(function() {
                alert('{{ __("Failed to load donation details. Please try again.") }}');
            });
    }

    function editActivity(id) {
        $('#editActivityModal').modal('show');

        $.get(`{{ url('donor-history/activity') }}/${id}`)
            .done(function(data) {
                $('#editActivityModal form').trigger("reset");
                $('#editActivityModal [name="id"]').val(data.id);
                $('#editActivityModal [name="donor_id"]').val(data.donor_id);
                $('#editActivityModal [name="activity_type"]').val(data.activity_type);
                $('#editActivityModal [name="call_type_id"]').val(data.call_type_id).trigger('change');
                if (data.call_type_id == 1) {
                    const statusContainer = document.getElementById('status-container');
                    statusContainer.style.display = 'block';
                }
                $('#editActivityModal [name="status"]').val(data.status).trigger('change');
                $('#editActivityModal [name="date_time"]').val(data.date_time);
                $('#editActivityModal [name="notes"]').val(data.notes);
                $('#editActivityModal [name="response"]').val(data.response);
            })
            .fail(function() {
                alert('{{ __("Failed to load activity details. Please try again.") }}');
            })
    };

    $(document).ready(function() {
        $('#editActivityForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var id = form.find('[name="id"]').val();
            

            $.ajax({
                url: `{{ url('activities') }}/${id}`,
                type: 'PUT',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editActivityModal').modal('hide');
                        form[0].reset();
                        // table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        location.reload(); // Fallback in case DataTables isn't used

                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            var input = form.find(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(errors[key][0]);
                        });
                    }
                }
            });
        });

    });
</script>
@endif