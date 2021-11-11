<?php

namespace App\Http\Controllers\admin\productManagement\orders;
use App\Http\Controllers\Controller;
use App\Mail\OrderCard;
use App\Models\Color;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
   public function show(Order $order)
   {
       Gate::authorize('show-order',$order);

       return view('admin.productManagement.orders.show',compact('order'));
   }
}
