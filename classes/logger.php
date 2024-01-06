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
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? json_encode($context) : '';

        $logEntry = sprintf("[%s] %s: %s %s\n", $timestamp, $level->value, $message, $contextJson);

        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX);
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
