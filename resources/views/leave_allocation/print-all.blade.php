@php
    $locale = app()->getLocale();
    $isRtl = in_array($locale, ['ar', 'fa', 'ur', 'pa', 'ps']);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <title class="text-3xl">{{ __('print.leave_allocation_title') }}</title>

    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">


    {{-- <link href="https://fonts.googleapis.com/css2?family=Lateef&display=swap" rel="stylesheet"> --}}

    <style>
        body {
            background: white;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            text-align: {{ $isRtl ? 'right' : 'left' }};
            font-family: {{ $isRtl ? "'Lateef', serif" : "'Segoe UI', sans-serif" }};
            margin: 2cm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: center;
        }
        thead {
            background-color: #f3f4f6; /* gray-100 */
            font-weight: 600;
            text-transform: uppercase;
            font-size: 22px
        }
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 2cm;
                font-size: 12px;
            }
        }
    </style>

    <script>
        window.onload = () => window.print();
    </script>
</head>
<body class="text-2xl font-lateef">

    <h2 class="mb-2 text-2xl font-bold text-center">{{ __('print.leave_allocation_title') }}</h2>
    <p class="mb-4 text-sm text-center text-gray-600">
        {{ __('print.generated_on') }} {{ now()->format('Y-m-d H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('print.leave_employee') }}</th>
                <th>{{ __('print.leave_leavetype') }}</th>
                <th>{{ __('print.total_days') }}</th>
                <th>{{ __('print.used_days') }}</th>
                <th>{{ __('print.remaining_days') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $count = 1;
            @endphp

            @forelse ($leave_allocations as $employee)
                @foreach($employee->leaveAllocations as $allocation)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $employee->name }} {{ $employee->last_name ?? '' }}</td>
                        <td>{{ $allocation->leaveType->name ?? '-' }}</td>
                        <td>{{ $allocation->total_days }}</td>
                        <td>{{ $allocation->used_days ?? 0 }}</td>
                        <td>{{ $allocation->total_days - ($allocation->used_days ?? 0) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('print.no_records') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
