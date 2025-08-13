<?php

namespace App\Models;

use App\Enums\DocumentStatusEnum;
use App\Enums\UtilityModelDocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityModelDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'utility_model_id',
        'document_type',
        'filename',
        'status',
        'comments',
        'commented_at',
        'revision_history',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'document_type' => UtilityModelDocumentTypeEnum::class,
        'status' => DocumentStatusEnum::class,
        'commented_at' => 'datetime',
    ];

    public function utilityModel(): BelongsTo
    {
        return $this->belongsTo(UtilityModel::class);
    }
}
