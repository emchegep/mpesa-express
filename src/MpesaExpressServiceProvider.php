<?php

namespace Emchegep\MpesaExpress;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Emchegep\MpesaExpress\Commands\MpesaExpressCommand;

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
