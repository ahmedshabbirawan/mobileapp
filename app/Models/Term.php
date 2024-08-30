<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use App\Traits\CurdBy;


class Term extends Model{
    use HasFactory, Definitions, CurdBy, SoftDeletes;  
    protected $table="terms";

    protected $fillable = [
        'name',
        'slug',
        'taxonomy',
        'description',
        'image',
        'parent',
        'count',
        'order',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $appends = ['status_label'];

    


    protected static function booted() {
        parent::boot();
        self::curdBy();
    }
    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }


    public function user(){
        return $this->belongsTo('App\Models\User','created_by');
    }
}
