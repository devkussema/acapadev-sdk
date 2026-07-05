<?php

namespace Acapadev\Sdk\Traits;

use Acapadev\Sdk\Facades\Acapadev;
use Illuminate\Support\Facades\Cache;

trait HasAcapadevRoles
{
    /**
     * Get the cache key for the user's Acapadev roles.
     */
    protected function getAcapadevRolesCacheKey(): string
    {
        return 'acapadev_roles_user_' . $this->getKey();
    }

    /**
     * Fetch the user's roles from Acapadev ID.
     */
    public function getAcapadevRoles(): array
    {
        return Cache::remember($this->getAcapadevRolesCacheKey(), 300, function () {
            try {
                return Acapadev::getUserRoles($this->getKey());
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Clear the cached roles.
     */
    public function clearAcapadevRolesCache(): void
    {
        Cache::forget($this->getAcapadevRolesCacheKey());
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasAcapadevRole(string $role): bool
    {
        $roles = $this->getAcapadevRoles();

        return in_array($role, $roles, true);
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyAcapadevRole(array $roles): bool
    {
        $userRoles = $this->getAcapadevRoles();

        return count(array_intersect($roles, $userRoles)) > 0;
    }
}
