<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function images(){
        return $this->morphTo();
    }

    public function getNameAttribute($value){
        if($this->images_type == 'App\Models\Xsession'){
            return asset('images/sessions/'.$value);

        }else{
            return asset('images/products/'.$value);
        }
    }
}
