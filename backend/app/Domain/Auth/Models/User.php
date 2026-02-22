<?php

declare(strict_types=1);

namespace App\Domain\Auth\Models;

use App\Domain\Tenant\Models\Tenant;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasUuid;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withPivot(['role', 'is_default', 'joined_at'])
            ->withTimestamps();
    }

    public function defaultTenant(): ?Tenant
    {
        return $this->tenants()
            ->wherePivot('is_default', true)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function roleInTenant(Tenant $tenant): ?string
    {
        $pivot = $this->tenants()
            ->where('tenants.id', $tenant->id)
            ->first()
            ?->pivot;

        return $pivot?->role;
    }

    public function isOwnerOf(Tenant $tenant): bool
    {
        return $this->roleInTenant($tenant) === 'owner';
    }

    public function belongsToTenant(Tenant $tenant): bool
    {
        return $this->tenants()
            ->where('tenants.id', $tenant->id)
            ->exists();
    }
}
