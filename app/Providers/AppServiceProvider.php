<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function (string $message, $object = null) {
            return response()->json([
                "isSuccess" => true,
                "message" => $message,
                "data"  => $object,
            ], 200);
        });

        Response::macro('error', function (string $message, $e = null) {
            return response()->json([
                "isSuccess" => false,
                "message" => $message,
                "data" => null,
                "errors"  => $e
            ], 200);
        });


    }
}
