<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffboardingChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'offboarding_id',
        'category',
        'task_name',
        'status',
        'responsible_role',
        'notes'
    ];

    public function offboarding()
    {
        return $this->belongsTo(Offboarding::class);
    }
}
