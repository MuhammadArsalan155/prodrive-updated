<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_method_id',
        'amount',
        'transaction_id',
        'status',
        'payment_details'
    ];

    // Optional: Cast certain fields
    protected $casts = [
        'amount' => 'float',
        'payment_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Derive student relationship through invoice if needed
    public function student()
    {
        return $this->hasOneThrough(Student::class, Invoice::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessor for formatted amount
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    // Check if payment is successful
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    // Generate a unique transaction ID
    public function generateTransactionId()
    {
        return 'TRX-' . uniqid() . '-' . time();
    }
}