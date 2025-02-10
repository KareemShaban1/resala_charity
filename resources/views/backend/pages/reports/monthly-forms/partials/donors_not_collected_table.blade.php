<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('Donor Name') }}</th>
            <th>{{ __('Address') }}</th>
            <th>{{ __('Phone') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($donorsWithNotCollectedForms as $donor)
            <tr>
                <td>{{ $donor->name }}</td>
                <td>{{ $donor->address }}</td>
                <td>
                    @foreach ($donor->phones as $phone)
                        {{ $phone->phone_number }} -- ({{ $phone->phone_type }})
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
