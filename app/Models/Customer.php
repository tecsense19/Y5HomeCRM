<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_customers';

    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'address',
        'city',
        'state',
        'country',
        'notes',
        'created_by',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getAllDocumentsAttribute()
    {
        $leadIds = $this->leads()->pluck('id')->toArray();
        $oppIds = $this->opportunities()->pluck('id')->toArray();
        $quotationIds = Quotation::where('customer_id', $this->id)
            ->orWhereIn('opportunity_id', $oppIds)
            ->pluck('id')
            ->toArray();

        return Document::where(function ($query) {
                $query->where('documentable_type', self::class)
                      ->where('documentable_id', $this->id);
            })
            ->orWhere(function ($query) use ($leadIds) {
                $query->where('documentable_type', Lead::class)
                      ->whereIn('documentable_id', $leadIds);
            })
            ->orWhere(function ($query) use ($oppIds) {
                $query->where('documentable_type', Opportunity::class)
                      ->whereIn('documentable_id', $oppIds);
            })
            ->orWhere(function ($query) use ($quotationIds) {
                $query->where('documentable_type', Quotation::class)
                      ->whereIn('documentable_id', $quotationIds);
            })
            ->with('documentable')
            ->latest()
            ->get();
    }

    public function scopeForUser($query, \App\Models\User $user)
    {
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            return $query;
        }
        if ($user->isExperienceCenterUser()) {
            return $query->where(function ($q) use ($user) {
                $q->whereHas('leads', function ($l) use ($user) {
                    $l->where('experience_center_id', $user->experience_center_id);
                })->orWhere('created_by', $user->id);
            });
        }
        // Sales Executive
        return $query->where(function ($q) use ($user) {
            $q->whereHas('leads', function ($l) use ($user) {
                $l->where('assigned_to', $user->id);
            })->orWhere('created_by', $user->id);
        });
    }
}
