@php
    $locale = app()->getLocale();
    $isRtl  = in_array($locale, ['ar', 'fa', 'ur', 'pa', 'ps']);   // RTL languages
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('print.department_name') }}</title>

    {{-- Tailwind (browser-ready) + your Vite CSS --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">

    <style>
        body{
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            text-align: {{ $isRtl ? 'right' : 'left' }};
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
            background-color: white;
        }
        table{
            border-collapse: collapse;
            width:100%;
        }
        th,td{
            border:1px solid gray;
            padding:6px;
            text-align:center;
        }
        @media print{
            @page{ margin:0; }
            body{ margin:2cm; font-size:12px; }
            .no-print{ display:none; }
        }
    </style>

    <script>window.onload = () => window.print();</script>
</head>
<body class="mt-3 font-lateef">

    {{-- heading --}}
    <h2 class="text-xl font-bold text-center">{{ __('print.department_name') }}</h2>
    <p class="text-sm text-center text-gray-500">
        {{ __('print.generated_on') }} {{ now()->format('Y-m-d H:i') }}
    </p>

    {{-- table --}}
    <div class="p-6 mx-auto max-w-7xl">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('print.department_name') }}</th>
                    <th>{{ __('print.department_code') }}</th>
                    <th>{{ __('print.department_head') }}</th>
                    <th>{{ __('print.department_description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $index => $department)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->code }}</td>
                        <td>{{ $department->department_head }}</td>
                        <td>{{ $department->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">{{ __('print.no_records') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
