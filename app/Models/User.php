<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use HasRoles;
    use HasPanelShield;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'postal_code',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function calendars()
    {
        return $this->belongsToMany(Calendar::class);
    }

    public function departaments()
    {
        return $this->belongsToMany(Departament::class);
    }

    //vacaciones
    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    //timesheets
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    //se onfigura para que pueda validar el ingreso a los paneles en este caso el de admin y personal 
    protected static function booted(): void
    {
        if (config('filament-shield.panel_user.enabled', false)) {
            FilamentShield::createRole(name: config('filament-shield.panel_user.enabled', 'panel_user'));
            static::created(function (User $user) {
                $user->assignRole(config('filament-shield.panel_user.enabled', 'panel_user'));
            });
            static::deleting(function (User $user) {
                $user->removeRole(config('filament-shield.panel_user.enabled', 'panel_user'));
            });
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole(Utils::getSuperAdminName());
        } elseif ($panel->getId() === 'personal') {
            return $this->hasRole(config('filament-shield.super_admin.name')) || $this->hasRole(config('filament-shield.panel_user.name', 'panel_user'));
        } else {
            return false;
        }
    }
}
