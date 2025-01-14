@if($activities->isEmpty())
<p>{{ __('No Activities found.') }}</p>
@else
<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Activity Type') }}</th>
            <th>{{ __('Call Type') }}</th>
            <th>{{ __('Date Time') }}</th>
            <th>{{ __('Created By') }}</th>


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

            </tr>
        @endforeach

    </tbody>
</table>
@endif