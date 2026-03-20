<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Collection;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles {
        hasRole as protected spatieHasRole;
        assignRole as protected spatieAssignRole;
        syncRoles as protected spatieSyncRoles;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->spatieHasRole($roles, $guard)) {
            return true;
        }

        $legacyRole = $this->getAttribute('role');

        if (!$legacyRole) {
            return false;
        }

        return collect($roles instanceof Collection ? $roles->all() : $roles)
            ->flatten()
            ->contains(fn ($role) => (string) $role === $legacyRole);
    }

    public function assignRole(...$roles)
    {
        $result = $this->spatieAssignRole(...$roles);
        $this->syncLegacyRoleColumn();

        return $result;
    }

    public function syncRoles(...$roles)
    {
        $result = $this->spatieSyncRoles(...$roles);
        $this->syncLegacyRoleColumn();

        return $result;
    }

    protected function syncLegacyRoleColumn(): void
    {
        $primaryRole = $this->roles()
            ->whereIn('name', ['Super Admin', 'Vendor'])
            ->value('name');

        if ($primaryRole && $this->getAttribute('role') !== $primaryRole) {
            $this->forceFill(['role' => $primaryRole])->saveQuietly();
        }
    }
}
