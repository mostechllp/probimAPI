<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffboardingSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'offboarding_id',
        'total_payable',
        'total_deductions',
        'net_payable',
        'status',
        'remarks'
    ];

    public function offboarding()
    {
        return $this->belongsTo(Offboarding::class);
    }
}
