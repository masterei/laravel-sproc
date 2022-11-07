<?php

namespace Ojenra\Sproc\Facades;

use Illuminate\Support\Facades\Facade;
use Ojenra\Sproc\SP as BaseSP;

class SP extends Facade
{
    protected static function getFacadeAccessor()
    {
        self::clearResolvedInstance(BaseSP::class);

        return BaseSP::class;
    }
}
