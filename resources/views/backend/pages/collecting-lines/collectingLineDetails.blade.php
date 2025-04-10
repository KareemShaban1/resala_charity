<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Collecting Line Report</title>
    <style>
        body {
            font-family: 'xbriyaz', sans-serif;
            /* Use a font that supports Arabic */
            direction: rtl;
            /* Right-to-left for Arabic */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: right;
            /* Align text to the right for Arabic */
        }

        th {
            background-color: #f2f2f2;
        }

        .child-row {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        <h1> خط سير مندوب</h1>
    </div>
   
    <table style="width: 100%; border: none; margin-bottom:20px">
        <tr style="border:none">
        <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Line Number') }}: {{ $additionalData['collecting_line_number'] }}
            </td>
            <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Representative') }}: {{ $additionalData['representative'] }}
            </td>
            <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Employee') }}: {{ $additionalData['employee'] }}
            </td>
            <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Driver') }}: {{ $additionalData['driver'] }}
            </td>
            <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Collecting Date') }} : {{ $additionalData['collecting_line_date'] }}
            </td>
            <td style="text-align: right; font-size: 18px; font-weight: bold; border:none">
                {{ __('Area') }}: {{ $additionalData['area_group'] }}
            </td>
            <!-- <td style="text-align: right; font-size: 18px; font-weight: bold;">إجمالي التبرعات: {{ $additionalData['total_donations'] }}</td> -->
        </tr>
    </table>





    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم المتبرع</th>
                <th>الهواتف</th>
                <th>تبرع عينى</th>
                <th>تبرع مالى</th>
                <th>العنوان</th>
                <th>وقت التحصيل</th>
                <th>ملاحظات</th>
                <th>هل تم التبرع</th>
                <th>رقم الأيصال المالى</th>
                <th>رقم الأيصال العينى</th>
            </tr>
        </thead>
        <tbody>
            @php $index = 1; @endphp
            @foreach ($organizedData as $parentId => $donationData)
            @php
            $parentDonation = $donationData['parent'];
            $childDonations = $donationData['children'];
            @endphp
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $parentDonation->donor_name }}</td>
                <td>{{ $parentDonation->phone_numbers }}</td>
                <td>
                    @foreach ($parentDonation->donateItems->where('donation_type', 'inKind') as $in_kind)
                    {{ $in_kind->item_name }} ({{ $in_kind->amount }})<br>
                    @endforeach
                </td>
                <td>
                    @foreach ($parentDonation->donateItems->where('donation_type', 'financial') as $financial)
                    {{ $financial->donationCategory?->name }} ({{ $financial->amount }})<br>
                    @endforeach
                </td>
                <td>{{ $parentDonation->address }}</td>
                <td>{{ $parentDonation->collecting_time }}</td>
                <td>{{ $parentDonation->notes }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($childDonations as $childDonation)
            <tr class="child-row">
                <td>{{ $index++ }}</td>
                <td>{{ $childDonation->donor_name }}</td>
                <td>{{ $childDonation->phone_numbers }}</td>
                <td>
                    @foreach ($childDonation->donateItems->where('donation_type', 'inKind') as $in_kind)
                    {{ $in_kind->item_name }} ({{ $in_kind->amount }})<br>
                    @endforeach
                </td>
                <td>
                    @foreach ($childDonation->donateItems->where('donation_type', 'financial') as $financial)
                    {{ $financial->donationCategory?->name }} ({{ $financial->amount }})<br>
                    @endforeach
                </td>
                <!-- <td>{{ $childDonation->address }}</td> -->
                 <td> /////////////// </td>
                <td>{{ $childDonation->collecting_time }}</td>
                <td>{{ $childDonation->notes }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>