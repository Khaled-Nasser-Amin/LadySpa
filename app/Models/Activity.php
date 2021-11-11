<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class Activity extends Model
{
    use HasFactory;

    protected $fillable=[
        'activity_action','ip_address','user_agent','vendor_id','belongs_to_id'
    ];

    public function activity(){
        return $this->belongsTo(User::class,'vendor_id');
    }
    public function activityBelongsTo(){
        return $this->belongsTo(User::class,'belongs_to_id');
    }

    public function getUserAgentAttribute($value){//agent->platform() , agent->browser();
        return tap(new Agent, function ($agent) use($value){
            $agent->setUserAgent($value);
       });
    }

}
