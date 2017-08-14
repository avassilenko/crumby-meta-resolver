<?php

namespace Crumby\MetaResolver\Facades;

use Illuminate\Support\Facades\Facade;

class MetaResolver extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MetaResolver';
    }
}
