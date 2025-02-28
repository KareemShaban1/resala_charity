<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('Donor Name') }}</th>
            <th>{{ __('Area') }}</th>
            <th>{{ __('Follow Up Department') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Collecting Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($donorsWithForms as $donor)

        <tr>
            <td>{{ $donor->name }}</td>
            <td>{{ $donor->area->name }}</td>
            <td>
                @foreach ($donor->monthlyForms as $monthlyForm)
                {{ $monthlyForm->followUpDepartment?->name ?? '' }}
                @endforeach
            </td>
            <td>
                @foreach ($donor->phones as $phone)
                {{ $phone->phone_number }}
                @endforeach
            </td>
            <td>
                @if($donor->collected_status === 'collected')
                <span class="text-white badge bg-success">{{ __('Collected') }}</span>
                @else
                <span class="text-white badge bg-danger">{{ __('Not Collected') }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>