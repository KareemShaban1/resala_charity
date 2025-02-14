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
                    <h5 class="modal-title" id="eventModalLabel">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="eventName" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">Save Event</button>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                select: function(info) {
                    $('#eventModal').modal('show');
                    document.getElementById('startDate').value = info.startStr;
                    // document.getElementById('endDate').value = info.endStr - 1;
                    // Convert end date to a JavaScript Date object, subtract one day, and format it back
                    let endDate = new Date(info.endStr);
                    endDate.setDate(endDate.getDate() - 1);
                    let formattedEndDate = endDate.toISOString().split('T')[0];

                    // Set the adjusted end date in the input field
                    document.getElementById('endDate').value = formattedEndDate;
                    console.log(info, formattedEndDate);
                },
                events: @json($events).map(event => {
                    let endDate = new Date(event.end_date);
                    endDate.setDate(endDate.getDate() + 1); // Subtract one day
                    let formattedEndDate = endDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD

                    return {
                        id: event.id,
                        title: event.title,
                        start: event.start_date, // No change needed
                        end: formattedEndDate, // Adjusted end date
                        description: event.description
                    };
                }),

                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\nDescription: ' + info.event.extendedProps.description);
                }
            });

            calendar.render();

            // Handle saving the event via AJAX
            document.getElementById('saveEvent').addEventListener('click', function() {
                var eventName = document.getElementById('eventName').value;
                var eventDescription = document.getElementById('eventDescription').value;
                var startDate = document.getElementById('startDate').value;
                var endDate = document.getElementById('endDate').value;

                if (eventName && startDate && endDate) {
                    $.ajax({
                        url: "{{ route('calendar.store') }}", // Update with your actual route
                        type: "POST",
                        data: {
                            title: eventName,
                            description: eventDescription,
                            start_date: startDate,
                            end_date: endDate,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Add event to FullCalendar
                            calendar.addEvent({
                                id: response.id,
                                title: response.title,
                                start: response.start_date,
                                end: response.end_date,
                                description: response.description
                            });

                            // Close modal and reset form
                            $('#eventModal').modal('hide');
                            document.getElementById('eventForm').reset();
                        },
                        error: function(xhr) {
                            alert('Error saving event');
                        }
                    });
                } else {
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>

    @endpush