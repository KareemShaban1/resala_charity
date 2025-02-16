<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EventController extends Controller
{
    // Display calendar
    public function calendar()
    {
        $events = Event::all();

        return view('backend.pages.events.calendar', compact('events'));
    }

    // Display all events

    public function index()
    {
        $events = Event::all();

        return view('backend.pages.events.index', compact('events'));
    }
    public function data()
    {
        $events = Event::all();
        return DataTables::of($events)
            ->addColumn('actions', function ($event) {
                return '
                <div class="btn-group">
                   <button class="btn btn-primary btn-sm edit-button" data-id="' . $event->id . '">Edit</button>
                    <button class="btn btn-danger btn-sm delete-button" data-id="' . $event->id . '">Delete</button>
                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function calendarEvents()
    {
        $events = Event::all();
        return response()->json($events);
    }

    // Show form to create a new event
    public function create()
    {
        return view('events.create');
    }

    // Store a new event
    public function storeCalendarEvent(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable',
            'status' => 'required|in:ongoing,cancelled,done',
        ]);

        $event = Event::create($request->all());

        // Create a notification with the event start date
        $event->notifications()->create([
            'title' => "New Event: {$event->title}",
            'message' => "An event '{$event->title}' is scheduled for " . \Carbon\Carbon::parse($event->start_date)->format('d M, Y'),
            'date' => $event->start_date, // Store event start date in notifications
            'status' => 'unread',
        ]);
        return response()->json($event);
    }

    // Show a single event
    public function show($id)
    {
        $event = Event::find($id);
        return response()->json($event);
    }

    // Edit an event
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    // Update an event
    public function update(Request $request,  $id)
    {
        $event  = Event::find($id);
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable',
            'status' => 'required|in:ongoing,cancelled,done',
        ]);

        $event->update($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.Event updated successfully')
        ]);
    }

    // Delete an event
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json([
            'success' => true,
            'message' => __('messages.Event deleted successfully')
        ]);
    }
}
