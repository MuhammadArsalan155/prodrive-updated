<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'student_id', 
        'course_id', 
        'amount', 
        'status', 
        'invoice_number'
    ];

    // Boot method to generate invoice number before creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Generate unique invoice number
            $invoice->invoice_number = self::generateInvoiceNumber();
        });
    }

    // Method to generate unique invoice number
    public static function generateInvoiceNumber()
    {
        // Get the last invoice to determine the next number
        $lastInvoice = self::orderBy('id', 'desc')->first();
        
        // Generate a formatted invoice number
        $prefix = 'INV-' . date('Ym'); // Format: INV-202402
        
        if (!$lastInvoice) {
            return $prefix . '-0001';
        }

        // Extract the last number and increment
        $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
        $newNumber = $lastNumber + 1;

        // Pad with zeros to maintain 4-digit format
        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}