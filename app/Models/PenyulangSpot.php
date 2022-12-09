<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyulangSpot extends Model
{
    protected $table = 'penyulang_spots';

    protected $fillable = [
        'penyulangs_id', 'code', 'header', 'name', 'type', 'latitude', 'longitude', 'created_by', 'updated_by'
    ];

    public $timestamps = true;
}
