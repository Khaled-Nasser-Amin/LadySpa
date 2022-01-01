<?php

namespace App\Http\Controllers\admin\productManagement\reservations;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Support\Facades\Gate;

class ReservationController extends Controller
{
   public function show(Reservation $reservation)
   {
       Gate::authorize('show-reservation',$reservation);

       return view('admin.productManagement.reservations.show',compact('reservation'));
   }
}
