<?php

namespace App\Http\Livewire\Admin\ProductsManagement\Sessions;

use App\Http\Controllers\admin\productManagement\sessions\SessionController;
use App\Models\Addition;
use App\Models\Tax;
use App\Models\Xsession;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SessionForm extends Component
{
    use WithFileUploads, AuthorizesRequests, ImageTrait;
    public
        $name_ar,
        $name_en,
        $time,
        $hour,
        $minutes,
        $taxes,
        $taxes_selected,
        $description_ar,
        $description_en,
        $image,
        $banner,
        $groupImage,
        $slug, $type,
        $price, $sale, $external_price, $external_sale, $external_service,
        $search;

    public $action; // action for change form action between add new product and update product
    public $session;

    //add addition
    public $addition_price, $addition_name_ar, $addition_name_en, $additions = [], $deletedAdditions = [];
    public $updateAddition, $update_addition_name_ar, $update_addition_name_en, $update_addition_price, $index_of_addition; // update size
    protected $listeners = ['edit'];

    public $index; //modal size and stock




    public function mount()
    {
        $this->taxes = Tax::get();
        $this->taxes_selected = [];
    }




    public function store()
    {
        $this->authorize('create',Xsession::class);

        $CreateSession = new SessionController();
        $data = $this->validation($this->imageValidationForStore());
        $data = $this->setSlug($data);
        $session = $CreateSession->store($data);
        $this->associateImagesWithSession($data, $session);
        auth()->user()->sessions()->save($session);
        $this->associateSessionWithAdditions($this->additions, $session);
        $session->taxes()->syncWithoutDetaching($this->taxes_selected);
        $this->resetVariables();
        $this->dispatchBrowserEvent('success', __('text.Session Added Successfully'));
        create_activity('Session Created', auth()->user()->id, $session->user_id);
    }


    public function edit()
    {
        $this->resetVariables();
        foreach ($this->session->additions as $row) {
            $this->additions[] = ['id' => $row->id, 'addition_name_ar' => $row->name_ar, 'addition_name_en' => $row->name_en, 'addition_price' => $row->price];
        }

        foreach ($this->session->additions()->onlyTrashed()->get() as $row) {
            $this->deletedAdditions[] = ['id' => $row->id, 'addition_name_ar' => $row->name_ar, 'addition_name_en' => $row->name_en, 'addition_price' => $row->price];
        }
        $this->name_ar = $this->session->name_ar;
        $this->name_en = $this->session->name_en;
        $this->taxes_selected = $this->session->taxes->pluck('id')->toArray();
        $this->description_ar = $this->session->description_ar;
        $this->description_en = $this->session->description_en;
        $this->slug = $this->session->slug;
        $this->time = $this->session->time;
        $time_arr=explode(':',$this->time);
        $this->hour = $time_arr[0];
        $this->minutes = $time_arr[1];
        $this->price = $this->session->price;
        $this->sale = $this->session->sale;
        $this->external_price = $this->session->external_price;
        $this->external_service = $this->external_price > 0 ? true:false;
        $this->external_sale = $this->session->external_sale;
        $this->emit('refreshMultiSelect');
    }

    public function update($id)
    {
        $this->authorize('update',$this->session);

        $sessionUpdate = new SessionController();
        $data = $this->validation($this->imageValidationForUpdate());

        $session = $sessionUpdate->update($data, $id);
        $this->associateSessionWithAdditions($this->additions, $session);
        if($this->groupImage){
            $this->livewireDeleteGroupOfImages($session->images,'sessions');
            $session->images()->delete();
            $this->associateImagesWithSession($data,$session);
        }
        if ($session->wasChanged()) {
            create_activity('Session Updated', auth()->user()->id, $session->user_id);
        }
        $this->dispatchBrowserEvent('success', __('text.Session Updated Successfully'));
    }

    public function render()
    {
        return view('components.admin.sessions.session-form');
    }

    public function validation($image_validation)
    {
        if ($this->external_service != true) {
            $this->external_price = null;
            $this->external_sale = null;
        }

        if($this->hour == "00" && $this->minutes == "00"){
            $this->time=null;


        }else{
            $this->time=$this->hour.":".$this->minutes;

        }
        return $this->validate(array_merge([
            'name_ar' => 'required|string|max:255|',
            'name_en' => 'required|string|max:255|',
            'slug' => 'nullable|string|max:255|',
            'description_ar' => 'nullable|string|max:255|',
            'description_en' => 'nullable|string|max:255|',
            'taxes_selected' => 'required|array|min:1',
            'taxes_selected.*' => 'exists:taxes,id',
            'price' => 'required|numeric',
            'sale' => 'nullable|numeric|lt:price',
            'external_price' => [Rule::requiredIf($this->external_service)],
            'external_sale' => 'nullable|numeric|lt:external_price',
            'additions' => 'nullable|array',
            'time' => 'required|date_format:H:i',
            'hour' => 'required|date_format:H',
            'minutes' => 'required|date_format:i',




        ], $image_validation));
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
    public function associateSessionWithAdditions($additions, $session)
    {
        $session->additions()->whereNotIn('id', collect($additions)->pluck('id')->toArray())->delete();

        foreach ($additions as $key => $row) {
            if (isset($row['id'])) {
                $addition = Addition::find($row['id']);
                if (!$addition) {
                    $addition = Addition::onlyTrashed()->findOrFail($row['id']);
                    $addition->restore();
                }
                $addition->update(['name_ar' => $row['addition_name_ar'], 'name_en' => $row['addition_name_en'], 'price' => $row['addition_price']]);
            } else {
                $addition = Addition::create(['name_ar' => $row['addition_name_ar'], 'name_en' => $row['addition_name_en'], 'price' => $row['addition_price']]);
                $addition->sessions()->associate($session->id);
                $addition->save();
                $this->additions[$key]['id'] = $addition->id;
            }
        }
    }

    //images
    public function associateImagesWithSession($data, $session)
    {
        $imagesNames = $this->livewireGroupImages($data, 'sessions');
        foreach ($imagesNames as $image)
            $session->images()->create(['name' => $image]);
    }


    //add new addition modal
    public function addAddition()
    {
        $this->addition_name_ar = strtolower($this->addition_name_ar);
        $this->addition_name_en = strtolower($this->addition_name_en);
        $this->validate([
            'addition_name_ar' => ['required', Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar')), Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en'))],
            'addition_name_en' => ['required', Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en')), Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar'))],
            'addition_price' => 'required|numeric|',
        ]);
        $this->additions[] = ['addition_name_ar' => $this->addition_name_ar, 'addition_name_en' => $this->addition_name_en, 'addition_price' => $this->addition_price];

        $this->resetVariablesAfterAddAddition();

        $this->emit('addAddition'); // emit to hide modal addition
    }

    public function updateAddition($index)
    {
        $this->update_addition_name_ar = $this->additions[$index]['addition_name_ar'];
        $this->update_addition_name_en = $this->additions[$index]['addition_name_en'];
        $this->update_addition_price = $this->additions[$index]['addition_price'];
        $this->index_of_addition = $index;
    }

    // update size completed
    public function updateAdditionComplete()
    {
        $this->update_addition_name_ar = strtolower($this->update_addition_name_ar);
        $this->update_addition_name_en = strtolower($this->update_addition_name_en);
        $this->validate([
            'update_addition_name_ar' => ['required', Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar')), Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en'))],
            'update_addition_name_en' => ['required', Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_en')), Rule::notIn(collect($this->additions)->except($this->index_of_addition)->pluck('addition_name_ar'))],
            'update_addition_price' => 'required|numeric',
        ]);
        $this->additions[$this->index_of_addition]['addition_name_ar'] = $this->update_addition_name_ar;
        $this->additions[$this->index_of_addition]['addition_name_en'] = $this->update_addition_name_en;
        $this->additions[$this->index_of_addition]['addition_price'] = $this->update_addition_price;
        $this->emit('updateAddition'); // emit to hide modal size

    }

    public function deleteAddition($index)
    {
        if (isset($this->additions[$index]['id']) && $this->additions[$index]['id'] > 0) {
            $this->deletedAdditions[] = ['id' => $this->additions[$index]['id'], 'addition_name_ar' => $this->additions[$index]['addition_name_ar'], 'addition_name_en' => $this->additions[$index]['addition_name_en'], 'addition_price' => $this->additions[$index]['addition_price']];
        }
        unset($this->additions[$index]);
        array_values($this->additions);
    }
    public function restoreAddition($index)
    {

        $this->additions[] = ['id' => $this->deletedAdditions[$index]['id'], 'addition_name_ar' => $this->deletedAdditions[$index]['addition_name_ar'], 'addition_name_en' => $this->deletedAdditions[$index]['addition_name_en'], 'addition_price' => $this->deletedAdditions[$index]['addition_price']];
        unset($this->deletedAdditions[$index]);
        array_values($this->deletedAdditions);
    }
    //end size and stock modal


    //resetVariables
    public function resetVariablesAfterAddAddition()
    {
        $this->addition_name_ar = '';
        $this->addition_name_en = '';
        $this->addition_price = '';
    }


    public function addedAllAdditions()
    {
        $this->validate(['additions' => 'nullable|array|min:0']);
        $this->emit('addedAllAdditions'); // emit to hide modal additions

    }
    public function resetVariables()
    {
        $this->reset([
            'name_ar', 'name_en',
            'description_ar', 'description_en', 'image', 'banner', 'price', 'sale',
            'groupImage', 'slug', 'taxes_selected', 'additions','time','hour','minutes','external_price','external_sale'
        ]);
    }




    //set slug when slug = null
    public function setSlug($data)
    {
        if ($this->slug == null) {
            $data['slug'] = $this->name_en . '-' . $this->name_ar;
        }
        return $data;
    }
}
