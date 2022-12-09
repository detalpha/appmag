<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyulang extends Model
{
    use SoftDeletes;

    protected $table = 'penyulangs';

    protected $fillable = [
        'name', 'code', 'gardu_induks_id', 'gardu_hubung_id', 'arus_hs_3_phs', 'teg_primer', 'teg_skunder', 
        'imp_trafo', 'updated_by', 'created_by'
    ];

    public $timestamps = true;

    public function penyulang_spot()
    {
        return $this->hasMany('App\Models\PenyulangSpot', 'penyulangs_id', 'id');
    }

    public function gardu_induk()
    {
        return $this->belongsTo('App\Models\GarduInduk', 'gardu_induks_id', 'id');
    }

    public function gardu_hubung()
    {
        return $this->belongsTo('App\Models\GarduInduk', 'gardu_hubung_id', 'id');
    }
}
