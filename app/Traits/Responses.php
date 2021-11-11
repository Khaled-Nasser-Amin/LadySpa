<?php

namespace App\Traits;

trait Responses{

    public function success($data=[],$message="",$status=200){
        return response()->json([
            'data'=>$data,
            'message'=>$message,
            'status'=>$status,
            ],$status);
    }
    public function error($message="",$status=404){
        return response()->json([
            'message'=>$message,
            'status'=>$status,
            ],$status);
    }
}

