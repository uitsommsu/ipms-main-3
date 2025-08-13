<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistanceForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inquiry',
        'filename',
        'response',
        'is_responded',
        'responded_at',
    ];

    protected $casts = [
        'is_responded' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
