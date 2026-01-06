<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_request_id',
        'product_id',
        'product_name',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function insuranceRequest()
    {
        return $this->belongsTo(InsuranceRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
