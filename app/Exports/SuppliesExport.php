<?php

namespace App\Exports;

use App\Models\Supply;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Supply::all();
        
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Quantity',
            'Unit',
            'Supplier',
            'Recipient',
            'Date',
            'Description',
        ];
    }

}
