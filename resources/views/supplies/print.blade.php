<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Supply</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @media print {
            @page { margin: 0; }
            body { margin: 2cm; font-size: 12px; color: black; }
            .no-print { display: none; }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
        }
    </style>
    <script>
        window.onload = () => window.print();
    </script>
</head>
<body class="max-w-4xl p-8 mx-auto text-gray-800 bg-white">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold tracking-wide text-gray-900">Supply Information</h1>
        <p class="mt-1 text-sm text-gray-500">Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>
   
 
    <table class="w-full overflow-hidden border border-gray-300 rounded-lg shadow-sm">
        <tbody class="text-sm">
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Product Name</th>
                <td class="px-4 py-2 border">{{ $supply->product_name }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Quantity</th>
                <td class="px-4 py-2 border">{{ $supply->quantity }} {{ $supply->unit }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Product Type</th>
                <td class="px-4 py-2 border">{{ $supply->product_type ?? 'N/A' }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Supplier</th>
                <td class="px-4 py-2 border">{{ $supply->supplier->name ?? 'N/A' }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Recipient</th>
                <td class="px-4 py-2 border">{{ $supply->recipient->name ?? 'N/A' }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Date</th>
                <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($supply->date)->format('d M Y') }}</td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <th class="px-4 py-2 font-semibold bg-gray-100 border">Description</th>
                <td class="px-4 py-2 border">{{ $supply->description ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-12">
        <h3 class="mb-2 text-lg font-semibold text-gray-700">Head Office Signature:</h3>
        <div class="w-1/2 h-20 border-b border-gray-400"></div>
    </div>
</body>
</html>
