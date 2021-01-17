<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Storage\Storage;

class StorageServiceProvider  extends ServiceProvider {
	 
	 public function register()
    {
        $this->app->singleton('mystorage', function ($app) {
            return new Storage();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
	
}

