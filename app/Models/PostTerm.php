<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Definitions;
use App\Traits\CurdBy;

class PostTerm extends Model
{
    use HasFactory;


    use HasFactory, Definitions, CurdBy;
    protected $fillable = ['post_id', 'term_id','name', 'slug','status','created_by','updated_by','deleted_by','deleted_at','created_at','updated_at'];   

    protected $table="post_terms";

   //  protected $appends = ['status_label'];

    protected static function booted() {
        parent::boot();
        self::curdBy();
    }
}
