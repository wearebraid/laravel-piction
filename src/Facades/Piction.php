<?php

namespace Wearebraid\Piction\Facades;

use Illuminate\Support\Facades\Facade;

class Piction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wearebraid-piction';
    }
}