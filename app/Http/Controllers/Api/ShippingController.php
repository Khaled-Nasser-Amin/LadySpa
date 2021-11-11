<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Shipping;
use App\Models\User;
use Illuminate\Http\Request;

class ShippingController extends Controller
{

    public function calc_shipping($vendors_lat_long,$lat,$long)
    {
        $setting=Setting::find(1);
        $shipping_status=$setting->shipping_status;


        if($shipping_status == 'by_city'){
            $city=$this->calc_shipping_by_city($lat,$long);

            $cost= $city ? round($city['cost'],2) : false;
        }

        if($shipping_status == 'by_kilometer' || ($cost == false && $shipping_status == 'by_city')){
            $distance=$this->calc_shipping_by_kilometer($vendors_lat_long);
            $cost=round($distance*$setting->shipping_cost_by_kilometer,2);
        }
        return $cost;
    }


    //calculate shipping by kilometer
    public function calc_shipping_by_kilometer($vendors_lat_long)
    {
        // get original point or set default
        $lady_store_geoLocation=User::where('role','admin')->first()->geoLocation;
        $latLong=explode(',',$lady_store_geoLocation);
        $point=$lady_store_geoLocation ? ['lat' => $latLong[0],'long' => $latLong[1]]
        : ['lat' => '29.999104','long' => '31.162367999999994'];

        $total_distance=0;

        //arrange vendors geoLocation consecutively
        foreach($vendors_lat_long as $vendor){
            $array_of_distance[]=[
                'lat'  => $vendor['lat'],
                'long'  => $vendor['long'],
                'distance'  => $this->distance($point['lat'],$point['long'],$vendor['lat'],$vendor['long']),
            ];

        }

        //calculate total distance between each other
        for($i = 1 ; $i < count($vendors_lat_long) ; $i++){
            $total_distance+= $this->distance($vendors_lat_long[$i-1]['lat'],$vendors_lat_long[$i-1]['long'],$vendors_lat_long[$i]['lat'],$vendors_lat_long[$i]['long']);
        }

        return $total_distance;

    }

    // calculate shipping by city
    protected function calc_shipping_by_city($lat,$long){
        $cities=Shipping::all();

        if(count($cities) > 0){
            foreach($cities as $city){
                $city_geoLocation=explode(',',$city->geoLocation);
                $lat1=$city_geoLocation[0];
                $long1=$city_geoLocation[1];
    
                $array[]=[
                    'id' => $city->id,
                    'city_name' => $city->city_name,
                    'cost' => $city->shipping_cost,
                    'geoLocation' => $city->geoLocation,
                    'distance' => $this->distance($lat1,$long1,$lat,$long),
                ];
    
            }
    
            $min_city=min(array_column($array,'distance'));
            $city=collect($array)->where('distance',$min_city)->collapse()->toArray();
            return $city;
        }else{
            return false;
        }
        
    }


    //calculate distance between two points
    protected function distance($lat1, $lat2, $lon1, $lon2) {

        //if result null use polyline instead
        $dist = $this->GetDrivingDistance($lat1, $lat2, $lon1, $lon2);

        if($dist == 0){
            $dist=$this->GetDistancePolyline($lat1, $lat2, $lon1, $lon2);
        }

        return $dist;

    }



    //Get Distance polyline
    public function GetDistancePolyline($lat1, $lat2, $lon1, $lon2){
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $kilometers = round(($dist * 60 * 1.1515)* 1.609344);

            return $kilometers;

          }
    }




    //Get Driving Distance
   public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyBmX3cxNy7VH9WLrzoh6FLGkjtZ0g3tLSE&origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['value'] ?? null;

        return round($dist/1000);
    }

}


