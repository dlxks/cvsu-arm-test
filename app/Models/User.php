<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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

    // Helper to check role
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superAdmin');
    }

    /**
     * Relationships declaration
     */
    /**
     * Get the profile associated with the Profile
     */
    public function facultyProfile(): HasOne
    {
        return $this->hasOne(FacultyProfile::class, 'user_id', 'id');
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class, 'user_id', 'id');
    }

    /**
     * Determine if the user can sign in through Google OAuth.
     */
    public function canUseGoogleSignIn(): bool
    {
        if ($this->trashed()) {
            return false;
        }

        if (! $this->hasAnyRole(['superAdmin', 'collegeAdmin', 'deptAdmin', 'faculty'])) {
            return false;
        }

        if ($this->hasRole(['collegeAdmin', 'deptAdmin']) && ! $this->employeeProfile()->exists()) {
            return false;
        }

        if ($this->hasRole('faculty') && ! $this->facultyProfile()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Persist Google account metadata for the user.
     */
    public function syncGoogleProfile(string $googleId, ?string $avatar): void
    {
        $this->forceFill([
            'google_id' => $googleId,
            'avatar' => $avatar,
            'email_verified_at' => $this->email_verified_at ?? now(),
        ])->save();
    }

    /**
     * Resolve the highest priority dashboard route for this user.
     */
    public function dashboardRoute(): ?string
    {
        if ($this->hasRole('superAdmin')) {
            return 'admin.dashboard';
        }

        if ($this->hasRole('collegeAdmin') && $this->employeeProfile()->exists()) {
            return 'college-admin.dashboard';
        }

        if ($this->hasRole('deptAdmin') && $this->employeeProfile()->exists()) {
            return 'department-admin.dashboard';
        }

        if ($this->hasRole('faculty') && $this->facultyProfile()->exists()) {
            return 'faculty.dashboard';
        }

        return null;
    }
}
