<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Products\Vendors_ProductsController;
use App\Http\Controllers\Api\Products\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Sessions\ReservationController;
use App\Http\Controllers\Api\Sessions\Vendors_SessionsController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\MyFatoorahController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['Auth:customer_api', 'scope:customer'])->post('/user', function (Request $request) {
    return response()->json($request->user(), 200);
});













//Routes for user
Route::group(['prefix' => 'user'], function () {

    Route::post('/resend-code', [AuthController::class, 'resend'])->middleware('throttle:5,1');


    //register
    Route::post('/active-account', [AuthController::class, 'activeAccount'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class, 'store']);

    //login
    Route::post('/auth_api', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout', [AuthController::class, 'logout']);



    //change passowrd
    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->middleware('throttle:5,1');
    Route::post('/check-otp', [AuthController::class, 'checkOtp'])->middleware('throttle:5,1');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('throttle:5,1');



    Route::group(['middleware' => ['Auth:customer_api', 'scope:customer']], function () {

        Route::post('/banners', [Vendors_ProductsController::class, 'banners']);

        //products
        Route::post('/products', [Vendors_ProductsController::class, 'vendor_products']);
        Route::post('/product_details', [Vendors_ProductsController::class, 'product_details']);
        Route::post('/vendors_products', [Vendors_ProductsController::class, 'vendors_products']);

        //sessions
        Route::post('/sessions', [Vendors_SessionsController::class, 'vendor_sessions']);
        Route::post('/session_details', [Vendors_SessionsController::class, 'session_details']);
        Route::post('/vendors_sessions', [Vendors_SessionsController::class, 'vendors_sessions']);

        //reviews
        Route::post('/review', [ReviewController::class, 'review']);
        Route::post('/all_reviews', [ReviewController::class, 'return_reviews']);


        //favorites
        Route::post('/favorite_product', [WishListController::class, 'updateWishListProduct']);


        //reservations
        Route::post('/available_time', [ReservationController::class, 'availableTime']);
        Route::post('/check_session_promocode', [ReservationController::class, 'check_promocode']);
        Route::post('/reservation', [ReservationController::class, 'store']);
        Route::post('/all_reservations', [ReservationController::class, 'all_reservations']);
        Route::post('/reservation_shipping', [ReservationController::class, 'reservation_shipping']);


        // Route::post('/cancel_order', [OrderController::class, 'cancel_order']);
        // Route::post('/check_stock', [OrderController::class, 'check_stock']);
        // Route::post('/all_orders', [OrderController::class, 'all_orders']);
        // Route::post('/order_details', [OrderController::class, 'order_details']);



        //orders
        Route::post('/check_promocode', [OrderController::class, 'check_promocode']);
        Route::post('/order', [OrderController::class, 'store']);
        Route::post('/cancel_order', [OrderController::class, 'cancel_order']);
        Route::post('/check_stock', [OrderController::class, 'check_stock']);
        Route::post('/all_orders', [OrderController::class, 'all_orders']);
        Route::post('/order_details', [OrderController::class, 'order_details']);
        Route::post('/calculate_shipping', [OrderController::class, 'calculate_shipping_api']);

        //profile
        Route::post('/change_image', [ProfileController::class, 'changeImage']);
        Route::post('/change_name', [ProfileController::class, 'changeName']);
        Route::post('/change_email', [ProfileController::class, 'changeEmail']);
        Route::post('/check_email_otp', [ProfileController::class, 'checkEmailOtp'])->middleware('throttle:5,1');
        Route::post('/change_phone', [ProfileController::class, 'changePhone']);
        Route::post('/check_phone_otp', [ProfileController::class, 'checkPhoneOtp'])->middleware('throttle:5,1');
        Route::post('/change_password', [ProfileController::class, 'changePassword']);
        Route::post('/resend_otp', [ProfileController::class, 'resend'])->middleware('throttle:5,1');

        //vendors
        // Route::post('/all_vendors', [VendorController::class, 'all_vendors']);
        // Route::post('/vendor_products', [VendorController::class, 'vendor_products']);


    });
});
//











//Routes for Delivery service provider
// Route::group(['prefix' => 'delivery_service_provider'], function () {

//     //resend
//     Route::post('/resend-code', [DeliveryAuthController::class, 'resend'])->middleware('throttle:5,1');
//     //

//     //register
//     Route::post('/active-account', [DeliveryAuthController::class, 'activeAccount'])->middleware('throttle:5,1');
//     Route::post('/register', [DeliveryAuthController::class, 'store']);

//     //


//     //login
//     Route::post('/login', [DeliveryAuthController::class, 'login'])->middleware('throttle:5,1');
//     Route::post('/logout', [DeliveryAuthController::class, 'logout']);
//     //

//     //change passowrd
//     Route::post('/forget-password', [DeliveryAuthController::class, 'forgetPassword'])->middleware('throttle:5,1');
//     Route::post('/check-otp', [DeliveryAuthController::class, 'checkOtp'])->middleware('throttle:5,1');
//     Route::post('/change-password', [DeliveryAuthController::class, 'changePassword'])->middleware('throttle:5,1');

//     //

//     Route::group(['middleware' => ['Auth:delivery_service_provider_api', 'scope:delivery']], function () {

//         //orders
//         Route::post('/all_orders', [DeliveryOrderController::class, 'all_orders']);
//         Route::post('/all_completed_orders', [DeliveryOrderController::class, 'all_completed_orders']);
//         Route::post('/order_details', [DeliveryOrderController::class, 'order_details']);
//         Route::post('/update_order_status', [DeliveryOrderController::class, 'updateOrderStatus']);
//         //

//         //profile
//         Route::post('/change_image', [DeliveryProfileController::class, 'changeImage']);
//         Route::post('/change_personal_id', [DeliveryProfileController::class, 'changePersonalId']);
//         Route::post('/change_driving_license', [DeliveryProfileController::class, 'changeDrivingLicense']);
//         Route::post('/change_name', [DeliveryProfileController::class, 'changeName']);
//         Route::post('/change_email', [DeliveryProfileController::class, 'changeEmail']);
//         Route::post('/check_email_otp', [DeliveryProfileController::class, 'checkEmailOtp'])->middleware('throttle:5,1');
//         Route::post('/change_phone', [DeliveryProfileController::class, 'changePhone']);
//         Route::post('/check_phone_otp', [DeliveryProfileController::class, 'checkPhoneOtp'])->middleware('throttle:5,1');
//         Route::post('/change_password', [DeliveryProfileController::class, 'changePassword']);
//         Route::post('/resend_otp', [DeliveryProfileController::class, 'resend'])->middleware('throttle:5,1');
//     });
// });

//
