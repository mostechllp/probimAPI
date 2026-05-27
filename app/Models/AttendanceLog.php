<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'company_id', 'userid', 'log_date', 'punch_in', 'punch_out', 'status', 'device_id', 'log_status',
        'punch_in_latitude', 'punch_in_longitude', 'punch_in_address',
        'punch_out_latitude', 'punch_out_longitude', 'punch_out_address'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(Employee::class, 'userid', 'employee_id');
    }
}
