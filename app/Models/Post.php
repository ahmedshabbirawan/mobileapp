<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Definitions;
use Carbon\Carbon;


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
        'thumbnail_id',
        'template_bounds',
        'post_type',
        'post_mime_type',
        'comment_count',
        'created_by'
    ];

    protected $appends = ['status_label'];



    public function postTerm()
    {
        return $this->belongsToMany(Term::class,'post_terms');
    }

    public function subView()
    {
        return $this->hasMany(SubView::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    public static function getMediaIdByFileName($fileName)
    {
        return self::where('guid',$fileName)->first();
    }

    protected static function booted() {
        parent::boot();
    }
    protected function getStatusLabelAttribute($value){
        return $this->post_status;
        // return self::statusLabel($this->status);
    }


    public function user(){
        return $this->belongsTo('App\Models\User','created_by');
    }
}
