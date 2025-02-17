@extends('backend.layouts.master')

@section('title')
{{__('Calendar')}}
@endsection

@push('styles')

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

@endpush
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">

                </div>
                <h4 class="page-title">{{__('Calendar')}}</h4>


            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div id="calendar"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal for Adding Events -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">{{ __('Add Event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <label for="eventName" class="form-label">{{ __('Title') }}</label>
                            <input type="text" class="form-control" id="eventName" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="startDate" class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">{{ __('End Date') }}</label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventStatus" class="form-label">{{ __('Status') }}</label>
                            <select class="form-control" id="eventStatus" name="status" required>
                                <option value="ongoing">{{ __('Ongoing') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                                <option value="done">{{ __('Done') }}</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-danger" id="deleteEvent" style="display: none;">{{ __('Delete') }}</button>
                    <button type="button" class="btn btn-primary" id="editEvent" style="display: none;">{{ __('Update') }}</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>


    <script>
    

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                select: function(info) {
                    $('#eventModal').modal('show');
                    $('#eventModalLabel').text('Add Event'); // Set modal title
                    $('#saveEvent').show(); // Show Save button
                    $('#editEvent').hide(); // Hide Edit button
                    $('#deleteEvent').hide(); // Hide Delete button

                    // Reset form and set start and end dates
                    $('#eventForm')[0].reset();
                    $('#startDate').val(info.startStr);
                    let endDate = new Date(info.endStr);
                    endDate.setDate(endDate.getDate() - 1);
                    $('#endDate').val(endDate.toISOString().split('T')[0]);
                },
                events: @json($events).map(event => {
                    let endDate = new Date(event.end_date);
                    endDate.setDate(endDate.getDate() + 1); // Adjust end date

                    let statusColors = {
                        'done': '#28a745',
                        'cancelled': '#dc3545',
                        'ongoing': '#007bff',
                    };

                    return {
                        id: event.id,
                        title: event.title,
                        start: event.start_date,
                        end: endDate.toISOString().split('T')[0],
                        description: event.description,
                        status: event.status,
                        backgroundColor: statusColors[event.status] || '#6c757d',
                        borderColor: statusColors[event.status] || '#6c757d',
                    };
                }),
                eventClick: function(info) {
                    let endDate = new Date(info.event.endStr);
                    endDate.setDate(endDate.getDate() - 1); // Adjust end date
                    $('#eventModal').modal('show');
                    $('#eventModalLabel').text('Edit Event'); // Set modal title
                    $('#saveEvent').hide(); // Hide Save button
                    $('#editEvent').show(); // Show Edit button
                    $('#deleteEvent').show(); // Show Delete button

                    console.log(endDate);
                    // Fill modal with event details
                    $('#eventName').val(info.event.title);
                    $('#eventDescription').val(info.event.extendedProps.description);
                    $('#startDate').val(info.event.startStr);
                    $('#endDate').val(endDate.toISOString().split('T')[0]);
                    $('#eventStatus').val(info.event.extendedProps.status);
                    $('#eventId').val(info.event.id); // Store event ID for updates

                    // Store event reference globally for editing
                    window.selectedEvent = info.event;
                }
            });

            calendar.render();

            // Handle saving a new event
            $('#saveEvent').click(function() {
                var eventData = {
                    title: $('#eventName').val(),
                    description: $('#eventDescription').val(),
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val(),
                    status: $('#eventStatus').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.post("{{ route('calendar.store') }}", eventData, function(response) {
                    let endDate = new Date(response.end_date);
                    endDate.setDate(endDate.getDate() + 1);

                    let statusColors = {
                        'done': '#28a745',
                        'cancelled': '#dc3545',
                        'ongoing': '#007bff',
                    };

                    calendar.addEvent({
                        id: response.id,
                        title: response.title,
                        start: response.start_date,
                        end: endDate.toISOString().split('T')[0],
                        description: response.description,
                        status: response.status,
                        backgroundColor: statusColors[response.status] || '#6c757d',
                        borderColor: statusColors[response.status] || '#6c757d',
                    });

                    $('#eventModal').modal('hide');
                    $('#eventForm')[0].reset();
                }).fail(function() {
                    alert('Error saving event');
                });
            });

            // Handle updating an event
            $('#editEvent').click(function() {
                if (!window.selectedEvent) return;

                var eventData = {
                    id: window.selectedEvent.id,
                    title: $('#eventName').val(),
                    description: $('#eventDescription').val(),
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val(),
                    status: $('#eventStatus').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('events.update', '') }}/" + window.selectedEvent.id, // Append ID
                    type: "PUT",
                    data: eventData,
                    success: function(response) {
                        let endDate = new Date(response.data.end_date);
                        endDate.setDate(endDate.getDate() + 1);


                        let statusColors = {
                            'done': '#28a745',
                            'cancelled': '#dc3545',
                            'ongoing': '#007bff',
                        };

                        console.log(endDate.toISOString().split('T')[0])
                       
                        let updatedEvent = {
                            id: response.data.id,
                            title: response.data.title,
                            start: response.data.start_date,
                            end: endDate.toISOString().split('T')[0],
                            description: response.data.description,
                            status: response.data.status,
                            backgroundColor: statusColors[response.data.status] || '#6c757d',
                            borderColor: statusColors[response.data.status] || '#6c757d',
                        };

                        window.selectedEvent.remove();
                        calendar.addEvent(updatedEvent);


                        $('#eventModal').modal('hide');
                    },
                    error: function() {
                        alert('Error updating event');
                    }
                });
            });

            // Handle deleting an event
            $('#deleteEvent').click(function() {
                if (!window.selectedEvent) return;

                if (confirm('Are you sure you want to delete this event?')) {
                    $.ajax({
                        url: "{{ route('events.destroy', '') }}/" + window.selectedEvent.id, // Append ID
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            window.selectedEvent.remove();
                            $('#eventModal').modal('hide');
                        },
                        error: function() {
                            alert('Error deleting event');
                        }
                    });
                }
            });
        });
    </script>

    @endpush