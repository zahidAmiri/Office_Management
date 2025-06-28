<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'ur', 'pa']) ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <title>{{ __('print.invoice_title') }}</title>
  {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
  <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">
  <style>
    @media print {
      @page { margin: 0; }
      body { margin: 2cm; font-size: 12px; }
      .no-print { display: none; }
    }
    body {

      background-color: white;
    }
    [dir="rtl"] {
      text-align: right;
    }
    [dir="rtl"] table {
      direction: rtl;
    }
  </style>
  <script>
    window.onload = () => window.print();
  </script>
</head>
<body class="border font-lateef">
  <h2 class="mt-5 text-5xl font-bold text-center">{{ __('print.invoice_title') }}</h2>
  <p class="text-sm text-center text-gray-500">{{ __('print.generated_on') }} {{ now()->format('Y-m-d H:i') }}</p>
  {{-- <p class="text-xs text-center text-gray-400 no-print">Laravel locale is: {{ app()->getLocale() }}</p> --}}

  <div class="p-6 mx-auto max-w-7xl">
    <div class="mb-8 text-center">
      <h1 class="text-4xl font-extrabold text-gray-800">{{ __('print.report_title') }}</h1>
    </div>

    <table class="w-full border border-gray-300 shadow-sm table-fixed">
      <thead class="text-sm tracking-wider text-gray-700 uppercase bg-gray-100">
        <tr>
          <th class="w-8 px-2 py-2 border">#</th>
          <th class="px-4 py-2 border">{{ __('print.product') }}</th>
          <th class="w-16 px-2 py-2 border">{{ __('print.quantity') }}</th>
          <th class="w-16 px-2 py-2 border">{{ __('print.unit') }}</th>
          <th class="px-2 py-2 border">{{ __('print.type') }}</th>
          <th class="px-4 py-2 border">{{ __('print.supplier') }}</th>
          <th class="px-4 py-2 border">{{ __('print.recipient') }}</th>
          <th class="w-32 px-3 py-2 border">{{ __('print.date') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($supplies as $index => $supply)
        <tr class="text-sm text-gray-800 odd:bg-white even:bg-gray-50">
          <td class="px-2 py-2 text-center border">{{ $index + 1 }}</td>
          <td class="px-4 py-2 border">{{ $supply->product_name }}</td>
          <td class="px-2 py-2 text-center border">{{ $supply->quantity }}</td>
          <td class="px-2 py-2 text-center border">{{ $supply->unit }}</td>
          <td class="px-2 py-2 text-center border">{{ $supply->product_type ?? '-' }}</td>
          <td class="px-4 py-2 border">{{ $supply->supplier?->name ?? '-' }}</td>
          <td class="px-4 py-2 border">{{ $supply->recipient?->name ?? '-' }}</td>
          <td class="px-3 py-2 text-center border">{{ \Carbon\Carbon::parse($supply->date)->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="py-4 text-center text-gray-500 border">
            {{ __('print.no_records') }}
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</body>
</html>
