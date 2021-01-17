<?php

namespace App\Facades;

class Storage extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'mystorage';
    }
}