<?php

namespace App\Models;

use App\Models\PatentTask;
use App\Enums\PatentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patent extends Model
{
    use HasFactory;

    protected $fillable = [
        'invention',
        'slug',
        'inventors',
        'description',
        'patent_number',
        'filing_date',
        'publication_date',
        'expiry_date',
        'status',
        'images',
        'downgraded_to_utility_model_at',
        'original_utility_model_id',
        'withdrawn_at',
        'abandoned_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'publication_date' => 'date',
        'expiry_date' => 'date',
        'downgraded_to_utility_model_at' => 'date',
        'withdrawn_at' => 'date',
        'abandoned_at' => 'date',
        'status' => PatentStatusEnum::class,
        'images' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::deleted(function (Patent $patent) {
            foreach ($patent->images as $image) {
                Storage::delete('public/' . $image);
            }
        });

        static::updating(function (Patent $patent) {
            if ($patent->getOriginal('images')) {

                $imagesToDelete = array_diff($patent->getOriginal('images'), $patent->images);

                foreach ($imagesToDelete as $image) {
                    Storage::delete('public/' . $image);
                }
            }
        });
    }

    public function proponents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'patent_proponent')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatentDocument::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PatentTask::class);
    }

    public function scopeNotDowngraded($query)
    {
        return $query->whereNull('downgraded_to_utility_model_at');
    }
    public function scopeDowngraded($query)
    {
        return $query->whereNotNull('downgraded_to_utility_model_at');
    }

    public function scopeWithdrawn($query)
    {
        return $query->whereNotNull('withdrawn_at');
    }
    public function scopeNotWithdrawn($query)
    {
        return $query->whereNull('withdrawn_at');
    }

    public function scopeAbandoned($query)
    {
        return $query->whereNotNull('abandoned_at');
    }

    public function scopeNotAbandoned($query)
    {
        return $query->whereNull('abandoned_at');
    }
}
