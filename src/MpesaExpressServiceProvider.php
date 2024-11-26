<?php

namespace Emchegep\MpesaExpress;

use Emchegep\MpesaExpress\Commands\MpesaExpressCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MpesaExpressServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mpesa-express')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_mpesa_express_table')
            ->hasCommand(MpesaExpressCommand::class);
    }
}
