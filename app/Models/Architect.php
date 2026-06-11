<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Architect extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_architects';

    protected $fillable = [
        'name',
        'firm_name',
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
