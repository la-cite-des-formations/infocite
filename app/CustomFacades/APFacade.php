<?php

namespace App\CustomFacades;

use Illuminate\Support\Facades\Facade;

class APFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ap';
    }
}
