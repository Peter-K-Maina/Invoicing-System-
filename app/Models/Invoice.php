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
        'description',
        'status',
];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
