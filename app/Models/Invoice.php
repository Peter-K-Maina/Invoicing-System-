<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'user_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'amount',
        'checkout_request_id',
        'transaction_ref',
        'mpesa_receipt',
        'paid_amount',
        'payer_phone',
        'paid_at',
        'description',
        'status',
        'mpesa_receipt',
];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
