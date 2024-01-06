<?php

enum LogLevel: string {
    case INFO = 'INFO';
    case ERROR = 'ERROR';
    case DEBUG = 'DEBUG';
}


class Logger {
    private $logFilePath;

    public function __construct($logFilePath) {
        $this->logFilePath = $logFilePath;
    }

    public function log(LogLevel $level, $message, $context = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level->value,
            'message' => $message,
            'context' => $context
        ];

        $jsonLogEntry = json_encode($logEntry) . "\n";
        file_put_contents($this->logFilePath, $jsonLogEntry, FILE_APPEND | LOCK_EX);
    }

    public function info($message, $context = []) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function error($message, $context = []) {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function debug($message, $context = []) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
