<?php

namespace Commando1251\LogViewer\Logs;

class PhpFpmLog extends Log
{
    public static string $name = 'PHP-FPM';
    public static string $regex = '/\[(?<datetime>[^\]]+)\] (?<level>\S+): (?<message>.*)/';
}
