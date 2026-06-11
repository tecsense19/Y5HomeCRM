<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'tbl_documents';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'category',
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'notes',
    ];

    public static function categories(): array
    {
        return [
            'site_photos'           => 'Site Photos',
            'quotation_pdf'         => 'Quotation PDF',
            'boq_pdf'               => 'BOQ PDF',
            'drawings'              => 'Drawings',
            'agreements'            => 'Agreements',
            'gst_certificate'       => 'GST Certificate',
            'pan_card'              => 'PAN Card',
            'agreement_copy'        => 'Agreement Copy',
            'business_registration' => 'Business Registration',
            'customer_drawings'     => 'Customer Drawings',
            'other'                 => 'Other',
        ];
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function scopeForUser($query, \App\Models\User $user)
    {
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            return $query;
        }
        
        $customerIds = Customer::forUser($user)->pluck('id');
        $leadIds = Lead::forUser($user)->pluck('id');
        $oppIds = Opportunity::forUser($user)->pluck('id');
        $quotationIds = Quotation::forUser($user)->pluck('id');
        
        return $query->where(function($q) use ($user, $customerIds, $leadIds, $oppIds, $quotationIds) {
            $q->where('uploaded_by', $user->id)
              ->orWhere(function($d) use ($customerIds) {
                  $d->where('documentable_type', Customer::class)
                    ->whereIn('documentable_id', $customerIds);
              })
              ->orWhere(function($d) use ($leadIds) {
                  $d->where('documentable_type', Lead::class)
                    ->whereIn('documentable_id', $leadIds);
              })
              ->orWhere(function($d) use ($oppIds) {
                  $d->where('documentable_type', Opportunity::class)
                    ->whereIn('documentable_id', $oppIds);
              })
              ->orWhere(function($d) use ($quotationIds) {
                  $d->where('documentable_type', Quotation::class)
                    ->whereIn('documentable_id', $quotationIds);
              });
        });
    }
}
