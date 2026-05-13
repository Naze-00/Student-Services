<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'request_id', 'student_id', 'service_type',
        'date_requested', 'status', 'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->request_id = 'REQ-' . strtoupper(uniqid());
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}