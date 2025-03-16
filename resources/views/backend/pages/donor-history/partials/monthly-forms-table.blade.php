@if($monthlyForms->isEmpty())
    <p>{{ __('No Monthly Forms Found') }}</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Created By') }}</th>
                <th>{{ __('Monthly Donation Day') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Created At') }}</th>
                <th>{{ __('Donates') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyForms as $monthlyForm)
            
                <tr>
                    <td>{{ $monthlyForm->id }}</td>
                    <td>{{ $monthlyForm->employee->name }}</td>
                    <td>{{ $monthlyForm->donor->monthly_donation_day }}</td>
                    <td>
                    {{ $monthlyForm->status === 'ongoing' ? __('Ongoing') : __('Cancelled')  }}
                    @if($monthlyForm->status === 'cancelled')
                        <br>
                        {{ $monthlyForm->cancellation_reason }}
                        <br>
                        {{ $monthlyForm->cancellation_date }}

                    @endif

                    </td>
                    <td>{{ $monthlyForm->created_at }}</td>
                    <td>
                        @foreach ($monthlyForm->items as $item)
                            <p>{{ $item->item_name ?? $item->donationCategory->name }} - {{ $item->amount }}</p>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
