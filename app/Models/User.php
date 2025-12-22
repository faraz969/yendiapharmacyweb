<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Concerns\IsFilamentUser;
use Filament\Models\Contracts\FilamentUser as FilamentUserContract;

class User extends Authenticatable implements FilamentUserContract
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, IsFilamentUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'branch_id',
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
        'email_verified_at' => 'datetime',
    ];

    /**
     * Determine if the user can access Filament admin panel.
     * Override the trait method to use Spatie Permission roles.
     *
     * @return bool
     */
    public function canAccessFilament(): bool
    {
        // Allow users with admin, manager, or staff roles
        return $this->hasAnyRole(['admin', 'manager', 'staff']);
    }

    // Relationships
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    public function defaultDeliveryAddress()
    {
        return $this->hasOne(DeliveryAddress::class)->where('is_default', true);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function isBranchStaff()
    {
        return $this->branch_id !== null;
    }
}
