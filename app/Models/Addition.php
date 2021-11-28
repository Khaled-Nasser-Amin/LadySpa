<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addition extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded=[];

    public function sessions()
    {
        return $this->belongsTo(Xsession::class,'session_id');
    }


}
