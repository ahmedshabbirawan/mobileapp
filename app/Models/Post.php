<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;


class Post extends Model{ //  implements Auditable{
    use HasFactory, Definitions,  SoftDeletes; 
    // \OwenIt\Auditing\Auditable,
    // CurdBy, 
    protected $table="posts";

    protected $fillable = [
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'original_file_name',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'template_bounds',
        'post_type',
        'post_mime_type',
        'comment_count',
        'created_by'
    ];

    protected $appends = ['status_label'];

    


    protected static function booted() {
        parent::boot();
      //   self::curdBy();
    }
    public function getStatusLabelAttribute($value){
        return self::statusLabel($this->status);
    }


    public function user(){
        return $this->belongsTo('App\Models\User','created_by');
    }
}
