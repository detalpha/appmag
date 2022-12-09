<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarduInduk extends Model
{
    use SoftDeletes;

    protected $table = 'gardu_induks';

    protected $fillable = [
        'name', 'code', 'latitude', 'longitude', 'gi_type', 'created_by', 'updated_by'
    ];

    public $timestamps = true;
}
