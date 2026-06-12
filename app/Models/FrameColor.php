<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrameColor extends Model
{
    protected $fillable = ['series', 'name', 'hex_code', 'is_active'];
}
