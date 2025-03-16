<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('Donor Name') }}</th>
            <th>{{ __('Area') }}</th>
            <th>{{ __('Follow Up Department') }}</th>
            <th>{{ __('Donates') }}</th>
            <th>{{ __('Total Amount') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Collecting Status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($donorsWithForms as $donor)
        @php
            // Collect all donations and sum the amounts
            $donationItems = [];
            $totalAmount = 0;

            foreach ($donor->monthlyForms as $monthlyForm) {
                foreach ($monthlyForm->donations as $donation) {
                    foreach ($donation->donateItems as $item) {
                        if ($item->donation_type === 'financial') {
                            $donationItems[] = $item->donationCategory?->name . " (" . $item->amount . ")";
                            $totalAmount += $item->amount;
                        }
                    }
                }
            }
        @endphp
        <tr>
            <td>{{ $donor->name }}</td>
            <td>{{ $donor->area?->name }}</td>
            <td>
                {{ implode(', ', $donor->monthlyForms->pluck('followUpDepartment.name')->filter()->unique()->toArray()) }}
            </td>
            <td>{{ implode(' --- ', $donationItems) }}</td>
            <td>{{ number_format($totalAmount, 2) }}</td>
            <td>{{ implode(' --- ', $donor->phones->pluck('phone_number')->toArray()) }}</td>
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
