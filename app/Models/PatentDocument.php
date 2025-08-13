<?php

namespace App\Models;

use App\Enums\DocumentStatusEnum;
use App\Enums\PatentDocumentTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patent_id',
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
        'document_type' => PatentDocumentTypeEnum::class,
        'status' => DocumentStatusEnum::class,
        'commented_at' => 'datetime',
    ];
}
