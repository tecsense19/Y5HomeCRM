<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_quotations';

    protected $fillable = [
        'quotation_number',
        'customer_id',
        'customer_name',
        'opportunity_id',
        'quotation_date',
        'quotation_value',
        'version_number',
        'prepared_by',
        'status',
        'notes',
        'items',
        'created_by',
    ];

    protected $casts = [
        'quotation_date'  => 'date',
        'quotation_value' => 'decimal:2',
        'version_number'  => 'integer',
        'items'           => 'array',
    ];

    const STATUS_DRAFT    = 'draft';
    const STATUS_SENT     = 'sent';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT    => 'Draft',
            self::STATUS_SENT     => 'Sent',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($quotation) {
            $last = static::withTrashed()->latest('id')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $quotation->quotation_number = 'QT' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            if (!$quotation->version_number) {
                $quotation->version_number = 1;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
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
                $q->whereHas('opportunity.lead', function ($l) use ($user) {
                    $l->where('experience_center_id', $user->experience_center_id);
                })->orWhere('created_by', $user->id)
                  ->orWhere('prepared_by', $user->id);
            });
        }
        // Sales Executive
        return $query->where(function ($q) use ($user) {
            $q->where('prepared_by', $user->id)
              ->orWhere('created_by', $user->id)
              ->orWhereHas('opportunity', function ($o) use ($user) {
                  $o->where('assigned_to', $user->id)
                    ->orWhereHas('lead', function ($l) use ($user) {
                        $l->where('assigned_to', $user->id);
                    });
              });
        });
    }
}
