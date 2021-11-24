<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\products\ProductController;
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
        $search;

    public $action; // action for change form action between add new product and update product
    public $product;

    //add size
    public $size,$price,$sale,$stock,$sizes=[],$deletedSizes=[];
    public $update_size,$update_stock,$update_price,$update_sale,$index_of_size; // update size
    protected $listeners=['edit','selected_product'];

    public $index; //modal size and stock




    public function mount(){
        $this->taxes=Tax::get();
        $this->taxes_selected=[];
        $this->products=Product::where('type','single')->where('user_id',auth()->user()->id)->get();
        $this->productsIndex[]=['product_id' => '','size' => '' ,'quantity' => '' ];
        $this->product_sizes[0]=[];
        $this->type="single";
    }

    //refresh jquery plugin
    public function updatedType(){
        if($this->type == 'group'){
            $this->products=Product::where('type','single')->where('user_id',auth()->user()->id)->get();

        }
        $this->emit('changeType');
    }



    public function store(){
        $productStore=new ProductController();
        if($this->type == 'single'){
            $data=$this->validation(array_merge(['sizes' =>'required|array|min:1'],$this->imageValidationForStore()));
        }else{
            $data=$this->validation(array_merge($this->imageValidationForStore(),$this->group_validation()));
        }

        $data=$this->setSlug($data);
        $product=$productStore->store($data);
        $this->associateImagesWithProduct($data,$product);


        auth()->user()->products()->save($product);

        if($this->type == 'single'){
            $this->associateProductWithSize($this->sizes,$product);

        }else{
            $this->groupType($product);
        }
        $product->taxes()->syncWithoutDetaching($this->taxes_selected);

        $this->resetVariables();
        $this->dispatchBrowserEvent('success', __('text.Product Added Successfully'));

        create_activity('Product Created',auth()->user()->id,$product->user_id);


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
            'type' => ['required',Rule::in(['single','group'])],


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

    //handle sizes array
    public function sizesAndPrice($product){
        foreach ($this->sizes as $key => $row){
            $size=Size::create([
                'size' => $row['size'],
                'stock' => $row['stock'],
                'price' => $row['price'],
                'sale' => trim($row['sale']) == '' || $row['sale'] == null ? 0 : $row['sale']
            ]);

            // $this->associateColorWithSize($row['sizes'],$color,$key);
            $product->sizes()->save($size);
        }
    }

    //sizes with product
    public function associateProductWithSize($sizes,$product){
        $product->sizes()->whereNotIn('id',collect($sizes)->pluck('id')->toArray())->delete();

        foreach ($sizes as $key=>$row)
        {
            if(isset($row['id'])){
                $size=Size::find($row['id']);
                if(!$size){
                    $size=Size::onlyTrashed()->findOrFail($row['id']);
                    $size->restore();
                }
                $size->update(['stock'=>$row['stock'],'price'=>$row['price'],'sale'=>$row['sale']]);

            }else{
                $size=Size::create(['size'=>$row['size'],'stock'=>$row['stock'],'price'=>$row['price'],'sale'=>$row['sale']]);
                $size->product()->associate($product->id);
                $size->save();
                $this->sizes[$key]['id']=$size->id;
            }

        }
    }

    //images
    public function associateImagesWithProduct($data,$product){
        $imagesNames=$this->livewireGroupImages($data,'products');
        foreach ($imagesNames as $image)
        Images::create(['name'=>$image])->product()->associate($product->id)->save();
    }


    // size and stock modal
    public function addSize($index){
        $this->size=strtolower($this->size);
        $this->validate([
            'size' => ['required',Rule::notIn(collect($this->sizes)->pluck('size')),Rule::notIn(collect($this->deletedSizes)->pluck('size'))],
            'stock' => 'required|integer|min:1',
            'price' => 'required|numeric|',
            'sale' => 'nullable|numeric|lt:price|',
        ]);
        $this->sizes[]=['size' => $this->size,'stock' => $this->stock,'price' => $this->price,'sale' => $this->sale];

        $this->resetVariablesAfterAddSize();

        $this->emit('addSize',$index); // emit to hide modal size
    }

    public function updateSize($index){
        $this->update_size=$this->sizes[$index]['size'];
        $this->update_stock=$this->sizes[$index]['stock'];
        $this->update_price=$this->sizes[$index]['price'];
        $this->update_sale=$this->sizes[$index]['sale'];
        $this->index_of_size=$index;
    }

    // update size completed
    public function updateSizeComplete($index){
        $this->update_size=strtolower($this->update_size);
        $this->validate([
            'update_size' => ['required',Rule::notIn(collect($this->sizes)->except($this->index_of_size)->pluck('size'))],
            'update_stock' => 'required|integer|min:1',
            'update_price' => 'required|numeric',
            'update_sale' => 'nullable|numeric|lt:update_price'
        ]);
        $this->sizes[$this->index_of_size]['size']=$this->update_size;
        $this->sizes[$this->index_of_size]['stock']=$this->update_stock;
        $this->sizes[$this->index_of_size]['price']=$this->update_price;
        $this->sizes[$this->index_of_size]['sale']=$this->update_sale;
        $this->emit('updateSize',$index); // emit to hide modal size

    }

    public function deleteSize($index){
        if(isset($this->sizes[$index]['id']) && $this->sizes[$index]['id'] > 0){
            $this->deletedSizes[]=['id'=>$this->sizes[$index]['id'],'size' => $this->sizes[$index]['size'],'stock' => $this->sizes[$index]['stock'],'price' => $this->sizes[$index]['price'],'sale'=>$this->sizes[$index]['sale'] ];

        }
        unset($this->sizes[$index]);
        array_values($this->sizes);
    }
    public function restoreSize($index){

        $this->sizes[]=['id'=>$this->deletedSizes[$index]['id'],'size' => $this->deletedSizes[$index]['size'],'stock' => $this->deletedSizes[$index]['stock'],'price' => $this->deletedSizes[$index]['price'],'sale'=>$this->deletedSizes[$index]['sale'] ];
        unset($this->deletedSizes[$index]);
        array_values($this->deletedSizes);
    }
    //end size and stock modal


    //resetVariables
    public function resetVariablesAfterAddSize(){
         $this->size='';
         $this->stock='';
         $this->price='';
         $this->sale='';
    }


    public function addedAllSizes(){
        $this->validate(['sizes'=>'required|array|min:1']);
        $this->emit('addedAllSizes'); // emit to hide modal sizes

    }
    public function resetVariables(){
        $this->reset([
            'name_ar','name_en',
            'description_ar','description_en','image','banner',
            'groupImage','slug','sizes','taxes_selected','group_price','group_sale','productsIndex'
        ]);
        $this->productsIndex[]=['product_id' => '','size' => '' ,'quantity' => '' ];

    }




    //set slug when slug = null
    public function setSlug($data){
        if ($this->slug == null){
            $data['slug'] = $this->name_en.'-'.$this->name_ar;
        }
        return $data;

    }




    //group of products


    //select product =>  change sizes
    public function selected_product($index,$product_id){
        $this->product_sizes[$index]=Product::findOrFail($product_id)->sizes;
    }

    public function addProduct(){
        $this->productsIndex[]=['product_id' => '','size' => '','quantity' => '' ];
        $this->product_sizes[]=[];
    }

    public function deleteProduct($index){

        unset($this->productsIndex[$index]);
        array_values($this->productsIndex);

        unset($this->product_sizes[$index]);
        array_values($this->product_sizes);

    }


    public function groupType($product){
        if ($this->type == 'group'){
            $product->child_products()->detach();
            $productsGroup=collect($this->productsIndex)->groupBy('product_id')->map(function ($value){
                $group_by_sizes= $value->groupBy('size')->map(function ($value2){
                     return ['size' =>$value2[0]['size'],'quantity' => $value2->sum('quantity')];
                 });
                 return  [$value[0]['product_id'],$group_by_sizes];
             });
            foreach ($productsGroup as $key =>$value){
                $child_product_id=$value[0];
                $product->child_products()->syncWithoutDetaching($child_product_id);
                foreach ($value[1] as $key =>$value){
                   $group_id=$product->child_products()->find($child_product_id)->pivot->id;
                   Size::find($value['size'])->groups()->syncWithoutDetaching([$group_id => ['quantity' => $value['quantity']]]);
                }
            }
        }

    }


    protected function group_validation()
    {
        return [
            'group_price' =>'required_if:type,group|numeric',
            'group_sale' =>'nullable|numeric|lt:group_price|',
            'productsIndex' =>'required_if:type,group|array|min:1',
            'productsIndex.*.product_id' => 'required_if:type,group|numeric|exists:products,id',
            'productsIndex.*.quantity' => 'required_if:type,group|numeric|min:1',
            'productsIndex.*.size' => 'required_if:type,group|numeric|exists:sizes,id',
        ];
    }

}
