<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_leads';

    protected $fillable = [
        'lead_number',
        'customer_id',
        'customer_name',
        'mobile_number',
        'email',
        'project_address',
        'city',
        'state',
        'country',
        'lead_source',
        'project_type',
        'construction_stage',
        'estimated_budget',
        'remarks',
        'assigned_to',
        'experience_center_id',
        'status',
        'lead_creation_date',
        'next_followup_date',
        'lost_reason',
        'created_by',
    ];

    protected $casts = [
        'lead_creation_date'  => 'date',
        'next_followup_date'  => 'date',
        'estimated_budget'    => 'decimal:2',
    ];

    // Status constants
    const STATUS_NEW            = 'new';
    const STATUS_CONTACTED      = 'contacted';
    const STATUS_QUALIFIED      = 'qualified';
    const STATUS_VISIT_SCHED    = 'site_visit_scheduled';
    const STATUS_VISIT_DONE     = 'site_visit_completed';
    const STATUS_QUOTATION_SENT = 'quotation_sent';
    const STATUS_NEGOTIATION    = 'negotiation';
    const STATUS_WON            = 'won';
    const STATUS_LOST           = 'lost';

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW            => 'New Lead',
            self::STATUS_CONTACTED      => 'Contacted',
            self::STATUS_QUALIFIED      => 'Qualified',
            self::STATUS_VISIT_SCHED    => 'Site Visit Scheduled',
            self::STATUS_VISIT_DONE     => 'Site Visit Completed',
            self::STATUS_QUOTATION_SENT => 'Quotation Sent',
            self::STATUS_NEGOTIATION    => 'Negotiation',
            self::STATUS_WON            => 'Won',
            self::STATUS_LOST           => 'Lost',
        ];
    }

    public static function sources(): array
    {
        return [
            'website'         => 'Website',
            'google_ads'      => 'Google Ads',
            'facebook'        => 'Facebook',
            'instagram'       => 'Instagram',
            'justdial'        => 'JustDial',
            'builder'         => 'Builder',
            'architect'       => 'Architect',
            'referral'        => 'Referral',
            'walk_in'         => 'Walk-In',
            'channel_partner' => 'Channel Partner',
        ];
    }

    public static function projectTypes(): array
    {
        return [
            'apartment'  => 'Apartment',
            'villa'      => 'Villa',
            'bungalow'   => 'Bungalow',
            'commercial' => 'Commercial',
            'office'     => 'Office',
        ];
    }

    public static function constructionStages(): array
    {
        return [
            'planning'         => 'Planning',
            'construction'     => 'Construction',
            'finishing'        => 'Finishing',
            'ready_possession' => 'Ready Possession',
        ];
    }

    public static function lostReasons(): array
    {
        return [
            'price_high'        => 'Price High',
            'competition'       => 'Competition',
            'no_response'       => 'No Response',
            'project_cancelled' => 'Project Cancelled',
            'project_delayed'   => 'Project Delayed',
            'other'             => 'Other',
        ];
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function experienceCenter()
    {
        return $this->belongsTo(ExperienceCenter::class, 'experience_center_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Auto-generate lead number
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($lead) {
            $last = static::withTrashed()->latest('id')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $lead->lead_number = 'LD' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            $lead->lead_creation_date = $lead->lead_creation_date ?? now()->toDateString();

            // Auto find or create customer
            if (empty($lead->customer_id) && !empty($lead->mobile_number)) {
                $customer = Customer::firstOrCreate(
                    ['mobile_number' => $lead->mobile_number],
                    [
                        'name'       => $lead->customer_name,
                        'email'      => $lead->email,
                        'city'       => $lead->city,
                        'state'      => $lead->state,
                        'country'    => $lead->country,
                        'created_by' => $lead->created_by ?? \Illuminate\Support\Facades\Auth::id() ?? 1
                    ]
                );
                $lead->customer_id = $customer->id;
            }
        });

        // Auto-create opportunity when lead is qualified
        static::saved(function ($lead) {
            if ($lead->status === self::STATUS_QUALIFIED) {
                Opportunity::firstOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'opportunity_number' => 'OPP' . str_pad($lead->id, 6, '0', STR_PAD_LEFT),
                        'customer_id'        => $lead->customer_id,
                        'customer_name'      => $lead->customer_name,
                        'stage'              => 'requirement_gathering',
                        'probability'        => 20,
                    ]
                );
            }
        });
    }

    // Scopes
    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            return $query;
        }
        if ($user->isExperienceCenterUser()) {
            return $query->where('experience_center_id', $user->experience_center_id);
        }
        return $query->where('assigned_to', $user->id);
    }
}
