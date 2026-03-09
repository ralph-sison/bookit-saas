<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Models;

use App\Domain\Auth\Models\User;
use App\Support\Traits\HasUuid;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 */
/**
 * @use HasFactory<TenantFactory>
 */
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'timezone',
        'currency',
        'settings',
        'is_active',
        'subscription_status',
        'stripe_customer_id',
        'stripe_subscription_id',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    protected static function newFactory(): \Database\Factories\TenantFactory
    {
        return \Database\Factories\TenantFactory::new();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsToMany<\App\Domain\Auth\Models\User, $this, \Illuminate\Database\Eloquent\Relations\Pivot, 'pivot'>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot(['role', 'is_default', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<\App\Domain\Auth\Models\User, $this, \Illuminate\Database\Eloquent\Relations\Pivot, 'pivot'>
     */
    public function owner(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
    }

    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial'
        /** @phpstan-ignore method.nonObject */
        && $this->trial_ends_at?->isFuture() === true;
    }

    public function isActive(): bool
    {
        return $this->is_active
            && in_array($this->subscription_status, ['trial', 'active']);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Tenant>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Tenant>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Tenant>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Tenant>
     */
    public function scopeBySlug(\Illuminate\Database\Eloquent\Builder $query, string $slug): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('slug', $slug);
    }
}
