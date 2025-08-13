<?php

namespace App\Models;

use App\Models\User;
use App\Models\UtilityModelTask;
use App\Enums\UtilityModelStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UtilityModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'researchers',
        'description',
        'um_number',
        'filing_date',
        'publication_date',
        'expiry_date',
        'status',
        'images',
        'original_patent_id',
        'upgraded_to_patent_at',
        'withdrawn_at',
        'abandoned_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'publication_date' => 'date',
        'expiry_date' => 'date',
        'status' => UtilityModelStatusEnum::class,
        'images' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::deleted(function (UtilityModel $um) {
            foreach ($um->images as $image) {
                Storage::delete('public/' . $image);
            }
        });

        static::updating(function (UtilityModel $um) {
            if ($um->getOriginal('images')) {
                $imagesToDelete = array_diff($um->getOriginal('images'), $um->images);

                foreach ($imagesToDelete as $image) {
                    Storage::delete('public/' . $image);
                }
            }
        });
    }

    public function proponents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'utility_model_proponent')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UtilityModelDocument::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(UtilityModelTask::class);
    }

    public function scopeNotUpgradedToPatent($query)
    {
        return $query->whereNull('upgraded_to_patent_at');
    }

    public function scopeUpgraded($query)
    {
        return $query->whereNotNull('upgraded_to_patent_at');
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
