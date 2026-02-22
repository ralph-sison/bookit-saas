<?php

declare(strict_types=1);

namespace App\Support\Traits;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (! app()->runningInConsole() && auth()->check()) {
                $model->tenant_id = $model->tenant_id ?? auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (! app()->runningInConsole() && auth()->check()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
