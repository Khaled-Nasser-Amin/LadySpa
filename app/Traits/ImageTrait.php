<?php

namespace App\Traits;

use App\Models\Images;
use Illuminate\Support\Facades\File;

trait ImageTrait
{
    public function AddSingleImage($request,$path){
        $request->validate(['image' => 'required|mimes:jpg,png,jpeg,gif,webp']);
        $file=$request->file('image');
        $fileName=time().'_'.$file->getClientOriginalName();
        $file->move($path,$fileName);
        return $fileName;
    }

    public function unlinkImage($path){
        if (File::exists($path))
            unlink($path);
    }

    public function AddGroupOfImages($request,$product,$folder){
        $images=[];
        if ($request->groupImage){
            $request->validate([
                'groupImage' => 'required|array|min:1',
                'groupImage.*' => 'mimes:jpeg,jpg,png,webp',
            ]);
            $files=$request->file('groupImage');
            foreach ($files as $file){
                $images[]= $fileName=time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images\\'.$folder),$fileName);
            }

        }
        return $images;
    }
    public function unlinkGroupOfImage($images,$folder){
        foreach ($images as $image){
            if(File::exists(public_path('images\\'.$folder.'\\'.$image->name)))
                unlink(public_path('images\\'.$folder.'\\'.$image->name));
        }
    }



    public function livewireAddSingleImage($request,$data,$folder){
        $path=$request['image']->store('public/'.$folder);
        $arr=explode('/',$path);
        $imageName=end($arr);
        $data['image']=$imageName;
        return $data;
    }

    public function livewireGroupImages($request,$folder){
        $images=[];
        if ($request['groupImage']){
            foreach ($request['groupImage'] as $image){
                $path=$image->store('public/'.$folder);
                $arr=explode('/',$path);
                $imageName=end($arr);
                $images[]=$imageName;
            }
        }
        return $images;
    }
    public function livewireDeleteSingleImage($model,$folder){
        if ($model->getAttributes()['image'] && File::exists(storage_path('app/public/'.$folder.'/'.$model->getAttributes()['image']))){
            unlink(storage_path('app\public\\'.$folder.'\\').$model->getAttributes()['image']);
        }
    }
    public function livewireDeleteGroupOfImages($images,$folder){
        foreach ($images as $image){
            if ($image->getAttributes()['name'] && File::exists(storage_path('app/public/'.$folder.'/'.$image->getAttributes()['name']))){
                unlink(storage_path('app\public\\'.$folder.'\\').$image->getAttributes()['name']);
            }
        }

    }
}
