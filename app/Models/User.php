<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $table = 'tbl_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role',
        'experience_center_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // Relationships
    public function experienceCenter()
    {
        return $this->belongsTo(ExperienceCenter::class, 'experience_center_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class, 'visited_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isSalesManager(): bool
    {
        return $this->role === 'sales-manager';
    }

    public function isSalesExecutive(): bool
    {
        return $this->role === 'sales-executive';
    }

    public function isExperienceCenterUser(): bool
    {
        return $this->role === 'experience-center';
    }
}
