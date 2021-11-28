<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\sessions\SessionController;
use App\Models\Addition;
use App\Models\Images;
use App\Models\Product;
use App\Models\Size;
use App\Models\Tax;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SessionForm extends Component
{
use WithFileUploads,AuthorizesRequests,ImageTrait;
    public
        $name_ar,
        $name_en,
        $taxes,
        $taxes_selected,
        $description_ar,
        $description_en,
        $image,
        $banner,
        $groupImage,
        $slug,$type,
        $price,$sale,
        $search;

    public $action; // action for change form action between add new product and update product
    public $product;

    //add addition
    public $addition_price,$addition_name_ar,$addition_name_en,$additions=[],$deletedAdditions=[];
    public $updateAddition,$update_addition_name_ar,$update_addition_name_en,$update_addition_price,$index_of_addition; // update size
    protected $listeners=['edit'];

    public $index; //modal size and stock




    public function mount(){
        $this->taxes=Tax::get();
        $this->taxes_selected=[];
    }




    public function store(){
        $CreateSession=new SessionController();
        $data=$this->validation($this->imageValidationForStore());
        $data=$this->setSlug($data);
        $session=$CreateSession->store($data);
        $this->associateImagesWithSession($data,$session);
        auth()->user()->sessions()->save($session);
        $this->associateSessionWithAdditions($this->additions,$session);
        $session->taxes()->syncWithoutDetaching($this->taxes_selected);
        $this->resetVariables();
        $this->dispatchBrowserEvent('success', __('text.Session Added Successfully'));
        create_activity('Session Created',auth()->user()->id,$session->user_id);
    }


    public function edit(){
        $this->resetVariables();
        foreach ($this->product->sizes as $row){
                $this->sizes[]=['id'=>$row->id,'size' => $row->size,'stock' => $row->stock,'price' => $row->price,'sale'=>$row->sale ];

        }

        foreach ($this->product->sizes()->onlyTrashed()->get() as $row){
            $this->deletedSizes[]=['id'=>$row->id,'size' => $row->size,'stock' => $row->stock,'price' => $row->price,'sale'=>$row->sale ];

        }
        $this->name_ar= $this->product->name_ar;
        $this->name_en=$this->product->name_en;
        $this->taxes_selected=$this->product->taxes->pluck('id')->toArray();
        $this->description_ar=$this->product->description_ar;
        $this->description_en=$this->product->description_en;
        $this->slug=$this->product->slug;
        $this->type=$this->product->type;
        $this->group_price=$this->product->group_price;
        $this->group_sale=$this->product->group_sale;

        if($this->type== 'group'){
            $this->productsIndex=[];
            $this->product_sizes=[];
            foreach($this->product->child_products()->get() as $product){
                foreach($product->pivot->sizes as $size){
                    $this->productsIndex[]=['product_id' => $product->id,'size' => $size->id,'quantity' => $size->pivot->quantity ];
                    $this->product_sizes[]=$product->sizes;
                }

            }
        }

        $this->emit('refreshMultiSelect');
    }

    public function update($id){
        $productUpdate=new ProductController();
        if($this->type == 'single'){
            $data=$this->validation(array_merge(['sizes' =>'required|array|min:1'],$this->imageValidationForUpdate()));
        }else{
            $data=$this->validation(array_merge($this->imageValidationForUpdate(),$this->group_validation()));
        }
        $product=$productUpdate->update($data,$id);
        if($this->type == 'single'){
            $this->associateProductWithSize($this->sizes,$product);

        }else{
            $this->groupType($product);
        }

        if($product->wasChanged()){
            create_activity('Product Updated',auth()->user()->id,$product->user_id);
        }
        $this->dispatchBrowserEvent('success', __('text.Product Updated Successfully'));


    }

    public function render()
    {
        return view('components.admin.sessions.session-form');
    }

    public function validation($image_validation){
        return $this->validate(array_merge([
            'name_ar' => 'required|string|max:255|',
            'name_en' => 'required|string|max:255|',
            'slug' => 'nullable|string|max:255|',
            'description_ar' => 'nullable|string|max:255|',
            'description_en' => 'nullable|string|max:255|',
            'taxes_selected'=>'required|array|min:1',
            'taxes_selected.*'=>'exists:taxes,id',
            'price' => 'required|numeric',
            'sale' => 'nullable|numeric|lt:price',
            'additions' =>'nullable|array',


        ],$image_validation));
    }

    //image validation
    protected function imageValidationForStore()
    {
        return [
            'image' => 'required|mimes:jpg,png,jpeg,gif',
            'banner' => 'required|mimes:jpg,png,jpeg,gif',
            'groupImage' => 'required|array|min:1',
            'groupImage.*' => 'mimes:jpeg,jpg,png,webp',
        ];
    }
    protected function imageValidationForUpdate()
    {
        return [
            'image' => 'nullable|mimes:jpg,png,jpeg,gif',
            'banner' => 'nullable|mimes:jpg,png,jpeg,gif',
            'groupImage' => 'nullable|array|min:1',
            'groupImage.*' => 'mimes:jpeg,jpg,png,webp',
        ];
    }

    //additions with session
    public function associateSessionWithAdditions($additions,$session){
        $session->additions()->whereNotIn('id',collect($additions)->pluck('id')->toArray())->delete();

        foreach ($additions as $key=>$row)
        {
            if(isset($row['id'])){
                $addition=Addition::find($row['id']);
                if(!$addition){
                    $addition=Addition::onlyTrashed()->findOrFail($row['id']);
                    $addition->restore();
                }
                $addition->update(['name_ar'=>$row['addition_name_ar'],'name_en'=>$row['addition_name_en'],'price'=>$row['addition_price']]);

            }else{
                $addition=Addition::create(['name_ar'=>$row['addition_name_ar'],'name_en'=>$row['addition_name_en'],'price'=>$row['addition_price']]);
                $addition->sessions()->associate($session->id);
                $addition->save();
                $this->additions[$key]['id']=$addition->id;
            }

        }
    }

    //images
    public function associateImagesWithSession($data,$session){
        $imagesNames=$this->livewireGroupImages($data,'sessions');
        foreach ($imagesNames as $image)
        $session->images()->create(['name'=>$image]);
    }


    //add new addition modal
    public function addAddition(){
        $this->addition_name_ar=strtolower($this->addition_name_ar);
        $this->addition_name_en=strtolower($this->addition_name_en);
        $this->validate([
            'addition_name_ar' => ['required',Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar')),Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en'))],
            'addition_name_en' => ['required',Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en')),Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar'))],
            'addition_price' => 'required|numeric|',
        ]);
        $this->additions[]=['addition_name_ar' => $this->addition_name_ar,'addition_name_en' => $this->addition_name_en,'addition_price' => $this->addition_price];

        $this->resetVariablesAfterAddAddition();

        $this->emit('addAddition'); // emit to hide modal addition
    }

    public function updateAddition($index){
        $this->update_addition_name_ar=$this->additions[$index]['addition_name_ar'];
        $this->update_addition_name_en=$this->additions[$index]['addition_name_en'];
        $this->update_addition_price=$this->additions[$index]['addition_price'];
        $this->index_of_addition=$index;
    }

    // update size completed
    public function updateAdditionComplete(){
        $this->update_addition_name_ar=strtolower($this->update_addition_name_ar);
        $this->update_addition_name_en=strtolower($this->update_addition_name_en);
        $this->validate([
            'update_addition_name_ar' => ['required',Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar')),Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en'))],
            'update_addition_name_en' => ['required',Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en')),Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar'))],
            'update_price' => 'required|numeric',
        ]);
        $this->additions[$this->index_of_addition]['addition_name_ar']=$this->update_addition_name_ar;
        $this->additions[$this->index_of_addition]['addition_name_en']=$this->update_addition_name_en;
        $this->additions[$this->index_of_addition]['addition_price']=$this->update_addition_price;
        $this->emit('updateAddition'); // emit to hide modal size

    }

    public function deleteAddition($index){
        if(isset($this->additions[$index]['id']) && $this->additions[$index]['id'] > 0){
            $this->deletedAdditions[]=['id'=>$this->additions[$index]['id'],'name' => $this->additions[$index]['name'],'price' => $this->sizes[$index]['price']];
        }
        unset($this->additions[$index]);
        array_values($this->additions);
    }
    public function restoreAddition($index){

        $this->additions[]=['id'=>$this->deletedAdditions[$index]['id'],'size' => $this->deletedAdditions[$index]['size'],'stock' => $this->deletedAdditions[$index]['stock'],'price' => $this->deletedSizes[$index]['price'],'sale'=>$this->deletedSizes[$index]['sale'] ];
        unset($this->deletedAdditions[$index]);
        array_values($this->deletedAdditions);
    }
    //end size and stock modal


    //resetVariables
    public function resetVariablesAfterAddAddition(){
         $this->addition_name_ar='';
         $this->addition_name_en='';
         $this->addition_price='';
    }


    public function addedAllAdditions(){
        $this->validate(['additions'=>'nullable|array|min:1']);
        $this->emit('addedAllAdditions'); // emit to hide modal additions

    }
    public function resetVariables(){
        $this->reset([
            'name_ar','name_en',
            'description_ar','description_en','image','banner','price','sale',
            'groupImage','slug','taxes_selected','additions'
        ]);

    }




    //set slug when slug = null
    public function setSlug($data){
        if ($this->slug == null){
            $data['slug'] = $this->name_en.'-'.$this->name_ar;
        }
        return $data;

    }


}
