<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'notes'
    ];

    protected $dates = [
        'due_date',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'status' => 'string'
    ];

    // Relationship with Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relationship with Student (through Invoice)
    public function student()
    {
        return $this->hasOneThrough(Student::class, Invoice::class);
    }

    // Scope for pending installments
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for overdue installments
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->whereDate('due_date', '<', Carbon::now());
    }

    // Check if installment is overdue
    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && 
               Carbon::parse($this->due_date)->isPast();
    }

    // Mark installment as paid
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = Carbon::now();
        $this->save();
    }

    // Generate reminder message
    public function getReminderMessageAttribute()
    {
        return sprintf(
            "Installment of $%.2f is due on %s. Please make your payment.",
            $this->amount,
            Carbon::parse($this->due_date)->format('F d, Y')
        );
    }
}