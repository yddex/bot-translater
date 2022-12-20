<?php
use Illuminate\Support\Facades\Date;

class Logger
{
    static function logUpdate($update)
    {
        file_put_contents(__DIR__ . '/logs/updates_log.txt', print_r($update, 1), FILE_APPEND);
    }

    static function logError(Exception | Throwable $e)
    {
        $log = $e->getMessage() . ' DATE: ' . (new DateTime())->format(DATE_RSS) . PHP_EOL;
        file_put_contents(__DIR__ . '/logs/error_log.txt', $log, FILE_APPEND);
    }
}