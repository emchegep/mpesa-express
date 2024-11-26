<?php

use Emchegep\MpesaExpress\MpesaExpress;

it('can create mpesa express', function () {
    $service = app(MpesaExpress::class);

    $uuid = $service->getUuid();

    expect($service)->toBeInstanceOf(MpesaExpress::class);
});
