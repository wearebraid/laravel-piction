<?php

namespace Braid\Piction\Facades;

use Illuminate\Support\Facades\Facade;

class Piction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'braid-piction';
    }
}