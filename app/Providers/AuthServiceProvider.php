<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('isAdmin', function (User $user) {
            return $user->role == "admin";
        });

        Gate::define('show-order',function(User $user,Order $order){
            return $user->orders->contains($order) || $user->role == 'admin';
        });

        Gate::define('delete-activity',function(User $user,Activity $activity){
            return $activity->vendor_id == $user->id || $user->role == 'admin';
        });
        Passport::routes();
        Passport::tokensCan([
            'customer' => 'customer',
        ]);
        Passport::loadKeysFrom(storage_path());

    }
}
