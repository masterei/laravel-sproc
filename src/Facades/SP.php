<?php

namespace Masterei\Sproc\Facades;

use Illuminate\Support\Facades\Facade;

class SP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Masterei\Sproc\SP::class;
    }
}
