<!-- Filter Inputs -->
<div class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" id="filterDonorName" class="form-control" placeholder="{{ __('Filter by Donor Name') }}">

        </div>
        <div class="col-md-4">
            <input type="text" id="filterArea" class="form-control" placeholder="{{ __('Filter by Area') }}">

        </div>
        <div class="col-md-4">
            <select id="filterStatus" class="form-control">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="collected">{{ __('Collected') }}</option>
                <option value="not_collected">{{ __('Not Collected') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
                <option value="follow up">{{ __('Follow Up') }}</option>
            </select>
        </div>
    </div>

</div>

<!-- Table -->
<table class="table table-bordered" id="donorTable">
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
        <tr class="donor-row"
            data-donor-name="{{ strtolower($donor->name) }}"
            data-area="{{ strtolower($donor->area?->name) }}"
            data-status="{{ $donor->collected_status }}">
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