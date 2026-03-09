<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Domain\Tenant\Models\Tenant
 */
class TenantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'subscription_status' => $this->subscription_status,
            'is_on_trial' => $this->isOnTrial(),
            'trial_ends_at' => $this->trial_ends_at,
            'role' => $this->whenPivotLoaded('tenant_user', fn () => $this->resource->getRelationValue('pivot')?->getAttribute('role')),
            'created_at' => $this->created_at,
        ];
    }
}
