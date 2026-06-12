<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExperienceCenter extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_experience_centers';

    protected $fillable = [
        'center_code',
        'center_name',
        'owner_name',
        'company_name',
        'gst_number',
        'pan_number',
        'msme_udyam_number',
        'mobile_number',
        'email',
        'address',
        'city',
        'state',
        'country',
        'agreement_start_date',
        'agreement_end_date',
        'security_deposit_amount',
        'status',
    ];

    protected $casts = [
        'agreement_start_date'    => 'date',
        'agreement_end_date'      => 'date',
        'security_deposit_amount' => 'decimal:2',
    ];

    const STATUS_ACTIVE    = 'active';
    const STATUS_INACTIVE  = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($center) {
            $last = static::withTrashed()->latest('id')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $center->center_code = 'EC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'experience_center_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'experience_center_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
