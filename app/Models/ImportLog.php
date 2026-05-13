<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = ['filename', 'user_id', 'summary_json', 'status'];

    protected $casts = ['summary_json' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}