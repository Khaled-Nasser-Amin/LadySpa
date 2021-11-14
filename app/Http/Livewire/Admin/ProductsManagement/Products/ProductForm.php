<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Products;

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


class ProductForm extends Component
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
        $product_banner,
        $groupImage,
        $slug,$type,
        $search;

    public $action; // action for change form action between add new product and update product
    public $product;

    //add size
    public $size,$price,$sale,$stock,$sizes=[];
    public $update_size,$update_stock,$update_price,$update_sale,$index_of_size; // update size
    protected $listeners=['edit'];

    public $index; //modal size and stock


    public $productsIndex; //group of products


    public function mount(){
        $this->taxes=Tax::get();
        $this->taxes_selected=[];

        $this->products=Product::where('type','single')->where('user_id',auth()->user()->id)->get();
        $this->productsIndex[]=['product_id' => '','quantity' => '' ];

        $this->type="single";


    }

    //refresh jquery plugin
    public function updatedType(){
        $this->emit('changeType');
    }
    public function store(){
        $productStore=new ProductController();
        $data=$this->validation(['image' => 'required|mimes:jpg,png,jpeg,gif','product_banner' => 'required|mimes:jpg,png,jpeg,gif']);
        $data=$this->setSlug($data);
        $product=$productStore->store($data);
        auth()->user()->products()->save($product);
        $this->associateProductWithSize($this->sizes,$product);
        $this->associateImagesWithProduct($data,$product);
        $product->taxes()->syncWithoutDetaching($this->taxes_selected);
        $this->resetVariables();
        $this->dispatchBrowserEvent('success', __('text.Product Added Successfully'));
        create_activity('Product Created',auth()->user()->id,$product->user_id);


    }


    // public function edit(){
        //     $this->authorize('update',$this->product);
        //     $this->resetVariables();
        //     foreach ($this->product->colors as $row){
        //         foreach($row->sizes as $size){
        //             $sizes[]=['id'=>$size->id,'size' => $size->size,'stock' => $size->stock];
        //         }
        //         $this->colorsIndex[]= ['id'=>$row->id,'color' => $row->color,'price'=> $row->price,'sale'=> $row->sale,'sizes'=> $sizes];
        //         $sizes=[];
        //     }
        //     $this->name_ar= $this->product->name_ar;
        //     $this->name_en=$this->product->name_en;
        //     $this->taxes_selected=$this->product->taxes->pluck('id')->toArray();
        //     $this->description_ar=$this->product->description_ar;
        //     $this->description_en=$this->product->description_en;
        //     $this->slug=$this->product->slug;
        //     $this->typeOfFabric=$this->product->typeOfFabric;
        //     $this->typeOfSleeve=$this->product->typeOfSleeve;
        //     $this->additions=$this->product->additions;
        //     $this->category_id=$this->product->category_id;

        //     $this->emit('refreshMultiSelect');
        // }

        // public function update($id){
        //     $this->authorize('update',$this->product);
        //     $productUpdate=new ProductController();
        //     $data=$this->validation(['image' => 'nullable|mimes:jpg,png,jpeg,gif']);
        //     $product=$productUpdate->update($data,$id);
        //     $this->updateColorsAndPrice ($product);
        //     if($product->wasChanged('category_id')){
        //         $new_cat=Category::find($product->category_id);
        //         $old_cat=Category::find($this->product->category_id);
        //         $this->updateCategoryStatus($new_cat);
        //         $this->deleteCategoryStatus($old_cat);
        //     }

        //     if($product->wasChanged()){
        //         create_activity('Product Updated',auth()->user()->id,$product->user_id);
        //     }

        //     $this->dispatchBrowserEvent('success', __('text.Product Updated Successfully'));


    // }

    public function render()
    {
        return view('components.admin.products.product-form');
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
            'sizes' =>'required|array|min:1',
            'groupImage' => 'required|array|min:1',
            'groupImage.*' => 'mimes:jpeg,jpg,png,webp',

        ],$image_validation));
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
                $size->update(['size'=>$row['size'],'stock'=>$row['stock'],'price'=>$row['price'],'sale'=>$row['sale']]);

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
            'size' => ['required',Rule::notIn(collect($this->sizes)->pluck('size'))],
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
        unset($this->sizes[$index]);
        array_values($this->sizes);
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
            'description_ar','description_en','image','product_banner',
            'groupImage','slug','sizes','taxes_selected'
        ]);

    }


    // active or unactive category
    protected function updateCategoryStatus($cat){
        if($cat->status == 1)
            return ;
        $cat->update(['status' => 1]);
        $cat->save();

        if($cat->parent_id == 0)
            return;
        $this->updateCategoryStatus($cat->parent_category);


    }

    protected function deleteCategoryStatus($cat){
        if($cat->products->where('isActive',1)->count() != 0 || $cat->child_categories->where('status',1)->count() > 0){
           return ;
        }else{
            $cat->update(['status' => 0]);
            $cat->save();
            if( $cat->parent_id == 0)
                return;
            $this->deleteCategoryStatus($cat->parent_category);

        }
    }

    //end active or unactive category




      //set slug when slug = null
      public function setSlug($data){
        if ($this->slug == null){
            $data['slug'] = $this->name_en.'-'.$this->name_ar;
        }
        return $data;

    }

}




// 'productsIndex' =>'required_if:type,group',
//             'productsIndex.*.product_id' => 'required_if:type,group|numeric|exists:products,id',
//             'productsIndex.*.quantity' => 'required_if:type,group|numeric|min:1',
//             public function groupType($product){
//                 if ($this->type == 'group'){
//                     $productsGroup=collect($this->productsIndex)->groupBy('product_id')->map(function ($value){
//                         return [$value[0]['product_id'] => $value->sum('quantity')];
//                     });
//                     foreach ($productsGroup as $key =>$value){
//                         $product->groups()->syncWithoutDetaching([$key=>['quantity'=>$value[$key]]]);
//                     }
//                 }else{
//                     $product->groups()->detach();
//                 }

//             }
//             public function resetVariables(){
//                 $this->reset(['name_ar','name_en','description_ar',
//                 'description_en','image','price','slug','type',
//                 'sale','phone','whatsapp','YearOfManufacture','groupImage',]);
//                 $this->categoriesIds=[];
//                 $this->productsIndex=[];
//                 $this->models=[];
//             }

//             public function addProduct(){
//                 $this->productsIndex[]=['product_id' => '','quantity' => '' ];
//             }

//             public function deleteProduct($index){
//                 unset($this->productsIndex[$index]);
//                 array_values($this->productsIndex);
//             }







