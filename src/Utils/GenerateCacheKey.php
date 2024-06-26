<?php

namespace Commando1251\LogViewer\Utils;

use Commando1251\LogViewer\Facades\LogViewer;
use Commando1251\LogViewer\LogFile;
use Commando1251\LogViewer\LogIndex;

class GenerateCacheKey
{
    public static function for(mixed $object, ?string $namespace = null): string
    {
        $key = '';

        if ($object instanceof LogFile) {
            $key = self::baseKey().':file:'.$object->identifier;
        }

        if ($object instanceof LogIndex) {
            $key = self::for($object->file).':'.$object->identifier;
        }

        if (is_string($object)) {
            $key = self::baseKey().':'.$object;
        }

        if (! empty($namespace)) {
            $key .= ':'.$namespace;
        }

        return $key;
    }

    protected static function baseKey(): string
    {
        return 'lv:'.LogViewer::version();
    }
}
