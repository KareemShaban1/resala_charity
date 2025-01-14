@if($donations->isEmpty())
<p>{{ __('No donations found.') }}</p>
@else
<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('ID') }}</th>
            <th>{{ __('Donation Type') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Created By') }}</th>
            <th>{{ __('Donates') }}</th>
            <th>{{ __('Donation Date') }}</th>
            <th>{{ __('Collecting Details') }}</th>

        </tr>
    </thead>
    <tbody>
        @foreach($donations as $donation)
        <tr>
            <td>{{ $donation->id }}</td>
            <td>@if ($donation->donation_type === 'inKind')
                {{ __('In-Kind') }}

                @elseif ($donation->donation_type === 'financial')
                {{ __('Financial') }}

                @else
                {{ __('Both') }}

                @endif
            </td>
            <td>{{ $donation->status === 'collected' ? __('Collected') : __('Not Collected') }}</td>
            <td>{{ $donation->createdBy->name }}</td>
            <td>
                @foreach ($donation->donateItems as $item)
                <div>

                    <span class="fw-bold me-2">
                        {{ $item->item_name ?? $item->donationCategory->name }} :
                    </span>
                    {{ $item->amount }}
                </div>
                @endforeach
            </td>
            <td>{{ $donation->date }}</td>
            <td>
                @if($donation->collectingDonation)

                <p>
                    <span class="fw-bold me-2">
                        {{ __('Employee') }}:
                    </span>
                    {{ $donation->collectingDonation->employee->name }}
                </p>
                <p>
                    <span class="fw-bold me-2">
                        {{ __('Donation Date') }}:
                    </span>
                    {{ $donation->collectingDonation->collecting_date }}
                </p>
                <p>

                    @if ($donation->donation_type === 'inKind')
                    <span class="fw-bold me-2">
                        {{ __('In Kind Receipt Number') }}:
                    </span>
                    {{ $donation->collectingDonation->in_kind_receipt_number ?? '' }}
                    @elseif ($donation->donation_type === 'financial')
                    <span class="fw-bold me-2">
                        {{ __('Financial Receipt Number') }}:
                    </span>
                    {{ $donation->collectingDonation->financial_receipt_number ?? '' }}
                    @else
                    <span class="fw-bold me-2">
                        {{ __('Financial Receipt Number') }}:
                    </span>
                    {{ $donation->collectingDonation->financial_receipt_number ?? '' }}
                    <span class="fw-bold me-2">
                        {{ __('In Kind Receipt Number') }}:
                    </span>-
                    {{ $donation->collectingDonation->in_kind_receipt_number ?? '' }}
                    @endif
                </p>



                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif