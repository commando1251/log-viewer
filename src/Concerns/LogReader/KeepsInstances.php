<?php

namespace Commando1251\LogViewer\Concerns\LogReader;

use Commando1251\LogViewer\LogFile;

trait KeepsInstances
{
    /**
     * Cached LogReader instances.
     */
    public static array $_instances = [];

    public static function instance(LogFile $file): static
    {
        if (! isset(static::$_instances[$file->path])) {
            static::$_instances[$file->path] = new static($file);
        }

        return static::$_instances[$file->path];
    }

    public static function clearInstance(LogFile $file): void
    {
        if (isset(static::$_instances[$file->path])) {
            unset(static::$_instances[$file->path]);
        }
    }

    public static function clearInstances(): void
    {
        static::$_instances = [];
    }
}
