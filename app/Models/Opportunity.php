<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_opportunities';

    protected $fillable = [
        'opportunity_number',
        'lead_id',
        'customer_id',
        'customer_name',
        'project_name',
        'expected_revenue',
        'expected_closing_date',
        'probability',
        'stage',
        'notes',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'expected_closing_date' => 'date',
        'expected_revenue'      => 'decimal:2',
        'probability'           => 'integer',
    ];

    const STAGE_REQUIREMENT  = 'requirement_gathering';
    const STAGE_PROPOSAL     = 'proposal';
    const STAGE_NEGOTIATION  = 'negotiation';
    const STAGE_WON          = 'won';
    const STAGE_LOST         = 'lost';

    public static function stages(): array
    {
        return [
            self::STAGE_REQUIREMENT => 'Requirement Gathering',
            self::STAGE_PROPOSAL    => 'Proposal',
            self::STAGE_NEGOTIATION => 'Negotiation',
            self::STAGE_WON         => 'Won',
            self::STAGE_LOST        => 'Lost',
        ];
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($opp) {
            if (empty($opp->opportunity_number)) {
                $last = static::withTrashed()->latest('id')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $opp->opportunity_number = 'OPP' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
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
                })->orWhere('created_by', $user->id);
            });
        }
        // Sales Executive
        return $query->where(function ($q) use ($user) {
            $q->where('assigned_to', $user->id)
              ->orWhere('created_by', $user->id)
              ->orWhereHas('lead', function ($l) use ($user) {
                  $l->where('assigned_to', $user->id);
              });
        });
    }
}
