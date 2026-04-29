<?php

class Logger {

    private static $file = 'api/logs/app.log';

    public static function error($message) {
        self::write('ERROR', $message);
    }

    public static function info($message) {
        self::write('INFO', $message);
    }

    private static function write($level, $message) {
        $date = date('Y-m-d H:i:s');
        $line = "[$date][$level] $message" . PHP_EOL;

        file_put_contents(self::$file, $line, FILE_APPEND);
    }
}
