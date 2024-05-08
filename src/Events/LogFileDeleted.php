<?php

namespace Commando1251\LogViewer\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Commando1251\LogViewer\LogFile;

class LogFileDeleted
{
    use Dispatchable;

    public function __construct(
        public LogFile $file
    ) {
    }
}
