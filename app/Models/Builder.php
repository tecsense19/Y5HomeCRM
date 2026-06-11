<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Builder extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_builders';

    protected $fillable = [
        'name',
        'company_name',
        'mobile_number',
        'email',
        'city',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
