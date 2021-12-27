<?php

namespace App\Http\Controllers\admin\productManagement\refunds;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;

class RefundController extends Controller
{
   public function show(Order $order)
   {
       return view('admin.productManagement.refunds.index');
   }
}
