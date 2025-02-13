<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Display calendar
    public function calendar()
    {
        return view('calendar');
    }

    // Display all events
    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
    }

    // Show form to create a new event
    public function create()
    {
        return view('events.create');
    }

    // Store a new event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable',
        ]);

        Event::create($request->all());
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    // Show a single event
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    // Edit an event
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    // Update an event
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable',
        ]);

        $event->update($request->all());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    // Delete an event
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
