@php
    $locale = app()->getLocale();                      // comes from the controller
    $isRtl  = in_array($locale, ['ar', 'fa', 'ur', 'pa', 'ps']);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('print.employee_title') }}</title>

    {{-- Tailwind build --}}
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">

    {{-- Optional: load Lateef font for RTL scripts --}}
    <link href="https://fonts.googleapis.com/css2?family=Lateef&display=swap" rel="stylesheet">

    <style>
        body{
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            text-align: {{ $isRtl ? 'right' : 'left' }};
            font-family: {{ $isRtl ? "'Lateef', serif" : "'Segoe UI', sans-serif" }};
            background-color:white;
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        th,td{
            border:1px solid #d1d5db;          /* gray-300 */
            padding:6px;
            text-align:center;
        }
        @media print{
            @page{ margin:0 }
            body{ margin:2cm; font-size:12px }
        }
    </style>

    <script>window.onload = () => window.print();</script>
</head>
<body>

    {{-- Heading --}}
    <h2 class="mt-3 text-2xl font-bold text-center">
        {{ __('print.employee_title') }}
    </h2>
    <p class="mb-4 text-sm text-center text-gray-500">
        {{ __('print.generated_on') }} {{ now()->format('Y-m-d H:i') }}
    </p>

    {{-- Employee table --}}
    <table>
        <thead class="bg-gray-100">
            <tr>
                <th>#</th>
                <th>{{ __('print.employee_name') }}</th>
                <th>{{ __('print.employee_fathername') }}</th>
                <th>{{ __('print.employee_ranking') }}</th>
                <th>{{ __('print.employee_contact') }}</th>
                <th>{{ __('print.employee_department') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($employees as $index => $emp)
                <tr class="{{ $index % 2 ? 'bg-gray-50' : 'bg-white' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $emp->name }} {{ $emp->last_name }}</td>
                    <td>{{ $emp->father_name }}</td>
                    <td>{{ $emp->ranking }}</td>
                    <td>{{ $emp->contact_number }}</td>
                    <td>{{ $emp->department?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">{{ __('print.no_records') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
