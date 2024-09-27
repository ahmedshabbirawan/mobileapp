<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubView extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'type','frame', 'text','font_name','font_size','text_color','image_name','extra','updated_by','deleted_by','deleted_at','created_at','updated_at'];   
    protected $appends = ['status_label'];


}

