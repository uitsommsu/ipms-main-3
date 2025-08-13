<?php

namespace App\Models;

use App\Models\UtilityModel;
use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Observers\UtilityModelTaskObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([UtilityModelTaskObserver::class])]
class UtilityModelTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'utility_model_id',
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

    public function utilityModel(): BelongsTo
    {
        return $this->belongsTo(UtilityModel::class);
    }
}
