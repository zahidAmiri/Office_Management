<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperimentalEquipmentsReturn extends Model
{
    protected $fillable = [
        'transfer_id', 'return_date', 'returned_condition', 'description',
    ];

    public function transfer()
    {
        return $this->belongsTo(ExperimentalEquipmentsTransfer::class);
    }
}
