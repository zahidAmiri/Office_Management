@php
    $locale = app()->getLocale();
    $isRtl = in_array($locale, ['ar', 'fa', 'ur', 'pa', 'ps']);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('print.add_leave_title') }}</title>

    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">

    {{-- <link href="https://fonts.googleapis.com/css2?family=Lateef&display=swap" rel="stylesheet"> --}}

    <style>
        body {
            background: white;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            text-align: {{ $isRtl ? 'right' : 'left' }};
            font-family: {{ $isRtl ? "'Lateef', serif" : "'Segoe UI', sans-serif" }};
            margin: 2cm;
            font-size: 12px;
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
            font-size: 0.85rem;
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

    <h2 class="mb-2 text-2xl font-bold text-center">{{ __('print.add_leave_title') }}</h2>
    <p class="mb-4 text-sm text-center text-gray-600">
        {{ __('print.generated_on') }} {{ now()->format('Y-m-d H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('print.add_leave_employee') }}</th>
                <th>{{ __('print.add_leave_leavetype') }}</th>
                <th>{{ __('print.add_leave_total_days') }}</th>
                <th>{{ __('print.add_leave_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($add_leaves as $index => $leave)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $leave->employee->name ?? '-' }}</td>
                    <td>{{ $leave->leaveType->name ?? '-' }}</td>
                    <td>{{ $leave->days }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('print.no_records') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
