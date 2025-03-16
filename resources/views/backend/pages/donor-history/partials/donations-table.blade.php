@if($donations->isEmpty())
<p>{{ __('No Donations Found') }}</p>
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
            <td>
            @if ($donation->status === 'collected')
            <span class="text-white badge bg-success">{{ __('Collected') }}</span>
            @elseif ($donation->status === 'not_collected')
            <span class="text-white badge bg-danger">{{ __('Not Collected') }}</span>
            @elseif($donation->status === 'followed_up')
            <span class="text-white badge bg-warning">{{ __('Followed Up') }}</span>
            @elseif($donation->status === 'cancelled')
            <span class="text-white badge bg-danger">{{ __('Cancelled Donation') }}</span>
            @endif

            <!-- {{ $donation->status === 'collected' ? __('Collected') : __('Not Collected') }}</td> -->
            <td>{{ $donation->createdBy->name }}</td>
            <td>
                @foreach ($donation->donateItems as $item)
                <div>
                    @if ($item->donation_type === 'financial')
                    <span class="fw-bold me-2">
                        {{ $item->donationCategory->name }} :
                    </span>
                    {{ $item->amount }} -----
                    ( {{ __('Receipt') }}: 
                    <span class="text-info"> {{ $item->financial_receipt_number }} </span>)
                    @else
                    <span class="fw-bold me-2">
                        {{ $item->item_name}} :
                    </span>
                    {{ $item->amount }} ----- 
                   ( {{ __('Receipt') }}: 
                   <span class="text-info">  {{ $donation->collectingDonation->in_kind_receipt_number ?? '' }} </span> )
                    @endif
                  
                </div>
                @endforeach
            </td>
            <td>{{ $donation->date }}</td>
            <td>
                @if($donation->collectingDonation)

                <p>
                    <span class="fw-bold me-2">
                        {{ __('Reporting way') }}
                    </span>
                    @if ($donation->reporting_way === 'call')
                    <span class="me-2">
                        {{ __('Call') }}:
                    </span>
                    @elseif($donation->reporting_way === 'whatsapp_chat')
                    <span class="me-2">
                        {{ __('WhatsApp Chat') }}:
                    </span>
                    @elseif($donation->reporting_way === 'location')
                    <span class="me-2">
                        {{ __('Location') }}:
                    </span>
                    @elseif($donation->reporting_way === 'other')
                    <span class="me-2">
                        {{ __('Other') }}:
                    </span>
                    @endif

                </p>
                <p>
                    <span class="fw-bold me-2">
                        {{ __('Employee') }}:
                    </span>
                    {{ $donation->collectingDonation->employee->name }}
                </p>

                <p>
                    <span class="fw-bold me-2">
                        {{ __('Collecting Date') }}:
                    </span>
                    {{ \Carbon\Carbon::parse($donation->collectingDonation->collecting_date)->format('d/m/Y') }}
                    </p>
            



                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif