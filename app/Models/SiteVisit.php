<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteVisit extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_site_visits';

    protected $fillable = [
        'visit_id',
        'lead_id',
        'customer_id',
        'customer_name',
        'visit_date',
        'visited_by',
        'location',
        'requirement_summary',
        'products_required',
        'estimated_project_value',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'visit_date'              => 'date',
        'products_required'       => 'array',
        'estimated_project_value' => 'decimal:2',
    ];

    public static function products(): array
    {
        return [
            'smart_switches'    => 'Smart Switches',
            'smart_locks'       => 'Smart Locks',
            'curtain_motors'    => 'Curtain Motors',
            'video_door_phone'  => 'Video Door Phone',
            'smart_lighting'    => 'Smart Lighting',
            'smart_sensors'     => 'Smart Sensors',
            'cctv'              => 'CCTV',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($visit) {
            $last = static::withTrashed()->latest('id')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $visit->visit_id = 'SV' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function visitedBy()
    {
        return $this->belongsTo(User::class, 'visited_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function scopeForUser($query, \App\Models\User $user)
    {
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            return $query;
        }
        if ($user->isExperienceCenterUser()) {
            return $query->where(function ($q) use ($user) {
                $q->whereHas('lead', function ($l) use ($user) {
                    $l->where('experience_center_id', $user->experience_center_id);
                })->orWhere('created_by', $user->id)
                  ->orWhere('visited_by', $user->id);
            });
        }
        // Sales Executive
        return $query->where(function ($q) use ($user) {
            $q->where('visited_by', $user->id)
              ->orWhere('created_by', $user->id)
              ->orWhereHas('lead', function ($l) use ($user) {
                  $l->where('assigned_to', $user->id);
              });
        });
    }
}
