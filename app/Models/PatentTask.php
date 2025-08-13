<?php

namespace App\Models;

use App\Models\Patent;
use App\Enums\TaskStatusEnum;
use App\Observers\PatentTaskObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;


#[ObservedBy([PatentTaskObserver::class])]
class PatentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'patent_id',
        'title',
        'description',
        'due_at',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
        'due_at' => 'datetime',
    ];

    public function patent(): BelongsTo
    {
        return $this->belongsTo(Patent::class);
    }
}
