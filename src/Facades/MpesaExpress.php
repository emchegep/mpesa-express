<?php

namespace Emchegep\MpesaExpress\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Emchegep\MpesaExpress\MpesaExpress
 */
class MpesaExpress extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Emchegep\MpesaExpress\MpesaExpress::class;
    }
}
