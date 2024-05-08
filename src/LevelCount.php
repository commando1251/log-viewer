<?php

namespace Commando1251\LogViewer;

use Commando1251\LogViewer\LogLevels\LevelInterface;

class LevelCount
{
    public function __construct(
        public LevelInterface $level,
        public int $count = 0,
        public bool $selected = false,
    ) {
    }
}
