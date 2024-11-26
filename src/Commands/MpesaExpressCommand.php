<?php

namespace Emchegep\MpesaExpress\Commands;

use Illuminate\Console\Command;

class MpesaExpressCommand extends Command
{
    public $signature = 'mpesa-express';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
