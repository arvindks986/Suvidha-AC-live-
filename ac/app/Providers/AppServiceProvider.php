<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \URL::forceScheme('https');
        Validator::extend('mobile', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match('/^[0-9]{10}$/', $value)) {
                return true;
            }
            return false;
        });

        Validator::extend('pin', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match('/^[0-9]{4}$/', $value)) {
                return true;
            }
            return false;
        });

        Validator::extend('password', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $value)) {
                return true;
            }
            return false;
        });
        Validator::extend('cpassword', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $value)) {
                return true;
            }
            return false;
        });
        Validator::extend('strong_password', function ($attribute, $value, $parameters) {
            // Contain at least one uppercase/lowercase letters, one number and one special char
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', (string)$value);
        }, 'Please make a strong password');

        Validator::extend('validstring', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match("/^[a-zA-Z0-9\s\-_,!:?'()&\s.]+$/", $value)) {
                return true;
            }
            return false;
        });

        Validator::extend('pan', function ($attribute, $value, $parameters) {
            if (!empty($value) && preg_match('/^(?:[0-9]+[a-z]|[a-z]+[0-9])[a-z0-9]*$/i', $value)) {
                return true;
            }
            return false;
        });

        Validator::extend('age', function ($attribute, $value, $parameters) {
            if (!empty($value) && $value <= 150 &&  $value >= 18) {
                return true;
            }
            return false;
        });

        Validator::extend('foldername', function ($attribute, $value, $parameters) {
            if (preg_match('/^[a-zA-Z0-9\.\-\_\s]+$/', $value)) {
                return true;
            }
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
