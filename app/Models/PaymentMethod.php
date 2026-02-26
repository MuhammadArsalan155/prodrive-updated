<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'logo' ,'additional_price','is_active'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }
    // Scope for active payment methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
