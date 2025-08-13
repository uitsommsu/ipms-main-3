<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'password',
        'agency',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => UserRoleEnum::class,
        'status' => UserStatusEnum::class,
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'management') {
            return Auth::user() && ($this->isAdmin() || $this->isManagement());
        }
        return Auth::check() && $this->isActive();
    }

    public function isAdmin(): bool
    {
        return $this->role == UserRoleEnum::ADMIN;
    }

    public function isManagement(): bool
    {
        return $this->role == UserRoleEnum::MANAGEMENT;
    }

    public function isResearcher(): bool
    {
        return $this->role == UserRoleEnum::RESEARCHER;
    }

    public function isActive(): bool
    {
        return $this->status == UserStatusEnum::ACTIVE;
    }

    public function patents(): BelongsToMany
    {
        return $this->belongsToMany(Patent::class, 'patent_proponent')->withTimestamps();
    }

    public function utilityModels(): BelongsToMany
    {
        return $this->belongsToMany(UtilityModel::class, 'utility_model_proponent')->withTimestamps();
    }

    public static function listOfAdminAndManagementUsers()
    {
        return self::query()
            ->where('role', UserRoleEnum::ADMIN)
            ->orWhere('role', UserRoleEnum::MANAGEMENT)
            ->get();
    }
}
