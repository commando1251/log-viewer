<?php

namespace Commando1251\LogViewer;

use Commando1251\LogViewer\Exceptions\CannotOpenFileException;
use Commando1251\LogViewer\Exceptions\SkipLineException;
use Commando1251\LogViewer\Logs\HorizonLog;
use Commando1251\LogViewer\Logs\HorizonOldLog;
use Commando1251\LogViewer\Logs\HttpAccessLog;
use Commando1251\LogViewer\Logs\HttpApacheErrorLog;
use Commando1251\LogViewer\Logs\HttpNginxErrorLog;
use Commando1251\LogViewer\Logs\LaravelLog;
use Commando1251\LogViewer\Logs\Log;
use Commando1251\LogViewer\Logs\LogType;
use Commando1251\LogViewer\Logs\PhpFpmLog;
use Commando1251\LogViewer\Logs\PostgresLog;
use Commando1251\LogViewer\Logs\RedisLog;
use Commando1251\LogViewer\Logs\SupervisorLog;

class LogTypeRegistrar
{
    private array $logTypes = [
        [LogType::LARAVEL, LaravelLog::class],
        [LogType::HTTP_ACCESS, HttpAccessLog::class],
        [LogType::HTTP_ERROR_APACHE, HttpApacheErrorLog::class],
        [LogType::HTTP_ERROR_NGINX, HttpNginxErrorLog::class],
        [LogType::HORIZON, HorizonLog::class],
        [LogType::HORIZON_OLD, HorizonOldLog::class],
        [LogType::PHP_FPM, PhpFpmLog::class],
        [LogType::POSTGRES, PostgresLog::class],
        [LogType::REDIS, RedisLog::class],
        [LogType::SUPERVISOR, SupervisorLog::class],
    ];

    public function register(string $type, string $class): void
    {
        if (! is_subclass_of($class, Log::class)) {
            throw new \InvalidArgumentException("{$class} must extend ".Log::class);
        }

        array_unshift($this->logTypes, [$type, $class]);
    }

    /**
     * @return string|Log|null
     */
    public function getClass(string $type): ?string
    {
        foreach ($this->logTypes as $logType) {
            if ($logType[0] === $type) {
                return $logType[1];
            }
        }

        return null;
    }

    public function guessTypeFromFirstLine(LogFile|string $textOrFile): ?string
    {
        if ($textOrFile instanceof LogFile) {
            $file = $textOrFile;
            try {
                $textOrFile = $textOrFile->getFirstLine();
            } catch (CannotOpenFileException $exception) {
                return null;
            }
        }

        foreach ($this->logTypes as [$type, $class]) {
            try {
                if ($class::matches($textOrFile)) {
                    return $type;
                }
            } catch (SkipLineException $exception) {
                // let's try the next 5 lines
                if (isset($file)) {
                    foreach (range(1, 5) as $lineNumber) {
                        try {
                            if ($class::matches($file->getNthLine($lineNumber))) {
                                return $type;
                            }
                        } catch (CannotOpenFileException $exception) {
                            return null;
                        } catch (SkipLineException $exception) {
                            continue;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function guessTypeFromFileName(LogFile $file): ?string
    {
        if (str_contains($file->name, 'laravel')) {
            return LogType::LARAVEL;
        } elseif (str_contains($file->name, 'php-fpm')) {
            return LogType::PHP_FPM;
        } elseif (str_contains($file->name, 'access')) {
            return LogType::HTTP_ACCESS;
        } elseif (str_contains($file->name, 'postgres')) {
            return LogType::POSTGRES;
        } elseif (str_contains($file->name, 'redis')) {
            return LogType::REDIS;
        } elseif (str_contains($file->name, 'supervisor')) {
            return LogType::SUPERVISOR;
        }

        return null;
    }
}
