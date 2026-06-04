<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffboardingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'offboarding_id',
        'letter_type',
        'document_path',
        'status'
    ];

    public function offboarding()
    {
        return $this->belongsTo(Offboarding::class);
    }
}
