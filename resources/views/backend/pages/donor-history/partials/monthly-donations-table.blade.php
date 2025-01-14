@if($monthlyDonations->isEmpty())
    <p>{{ __('No monthly donations found.') }}</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Donation Number') }}</th>
                <th>{{ __('Created By') }}</th>
                <th>{{ __('Collecting Way') }}</th>
                <th>{{ __('Donates') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyDonations as $monthlyDonation)
            
                <tr>
                    <td>{{ $monthlyDonation->id }}</td>
                    <td>{{ $monthlyDonation->number }}</td>
                    <td>{{ $monthlyDonation->employee->name }}</td>
                    <td>{{ $monthlyDonation->collecting_donation_way }}</td>
                    <td>
                        @foreach ($monthlyDonation->donates as $item)
                            <p>{{ $item->item_name ?? $item->donationCategory->name }} - {{ $item->amount }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
