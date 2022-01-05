<?php

namespace App\Http\Controllers\admin\productManagement\sessions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Traits\ImageTrait;
use App\Models\Xsession;
use Illuminate\Support\Facades\Request;

class SessionController extends Controller
{
    use ImageTrait;

    public function store($request)
    {
        $this->authorize('create',Xsession::class);

        $data=collect($request)->except(['image','additions','taxes_selected','banner','groupImage'])->toArray();
        $data['image']=$this->add_single_image($request['image'],'sessions');
        $data['banner']=$this->add_single_image($request['banner'],'sessions');
        $session=Xsession::create($data);
        return $session;
    }



    public function update($request,$id)
    {
        $session=Xsession::findOrFail($id);
        $this->authorize('update',$session);

        $data=collect($request)->except(['image','additions','taxes_selected','banner','groupImage'])->toArray();
        $this->updateImage($request,$session,$data);
        $session->update($data);
        $session->taxes()->detach();
        $session->taxes()->syncWithoutDetaching($request['taxes_selected']);
        $session->save();
        return $session;
    }

    protected function updateImage($request,$session,&$data){
        if ($request['image']){
            if(!$session->has('reservations')){
                $this->delete_single_image($session,'image');
            }
            $data['image']=$this->add_single_image($request['image'],'sessions');
        }

        if ($request['banner']){
            if(!$session->has('reservations')){
                $this->delete_single_image($session,'banner');
            }
            $data['banner']=$this->add_single_image($request['banner'],'sessions');
        }

    }
    public function destroy($session)
    {
        $this->authorize('delete',$session);

        $vendor_id=$session->user_id;
        $session->delete();
        return $vendor_id;
    }

    protected function add_single_image($image,$folder){
        $path=$image->store('public/'.$folder);
        $arr=explode('/',$path);
        $imageName=end($arr);
        return $imageName;
    }
    protected function delete_single_image($session,$attr){
        if ($session->getAttributes()[$attr] && File::exists(storage_path('app/public/sessions/'.$session->getAttributes()[$attr]))){
            unlink(storage_path('app\public\sessions\\').$session->getAttributes()[$attr]);
        }
    }

    public function show(Request $request,Xsession $session,$slug){
        $this->authorize('view',$session);

        $images= array_merge([$session->image],$session->images->pluck('name')->toArray());
        return view('admin.productManagement.sessions.show',compact('session','images'));
    }
    public function addNewSession(){
        $this->authorize('create',Xsession::class);
        return view('admin.productManagement.sessions.create');
    }
    public function updateSession(Xsession $session){
        $this->authorize('update',$session);
        return view('admin.productManagement.sessions.edit',compact('session'));
    }









}
