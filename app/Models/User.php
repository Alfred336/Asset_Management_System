<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'timezone'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'timezone'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

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

    /**
     * Get companies assigned to the user.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')->withTimestamps();
    }

    /**
     * Determine whether the user can access all companies/workplaces.
     */
    public function canAccessAllCompanies(): bool
    {
        return $this->hasAnyRole(['Super Admin', 'System Admin']);
    }

    /**
     * Get list of company IDs accessible by the user.
     *
     * @return array<int>
     */
    public function accessibleCompanyIds(): array
    {
        if ($this->canAccessAllCompanies()) {
            return Company::pluck('id')->toArray();
        }

        return $this->companies()->pluck('companies.id')->map(fn ($id) => (int) $id)->toArray();
    }

    /**
     * Check if user has access to a specific company ID.
     */
    public function hasAccessToCompany(int|string|null $companyId): bool
    {
        if (! $companyId) {
            return false;
        }

        if ($this->canAccessAllCompanies()) {
            return true;
        }

        return in_array((int) $companyId, $this->accessibleCompanyIds(), true);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's timezone as a DateTimeZone object.
     */
    public function timezoneZone(): \DateTimeZone
    {
        return new \DateTimeZone($this->timezone ?? 'UTC');
    }
}

