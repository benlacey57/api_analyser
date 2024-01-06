<?php

class Logger {
    private $logFilePath;

    const INFO = 'INFO';
    const ERROR = 'ERROR';
    const DEBUG = 'DEBUG';

    public function __construct($logFilePath) {
        $this->logFilePath = $logFilePath;
    }

    public function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? json_encode($context) : '';

        $logEntry = sprintf("[%s] %s: %s %s\n", $timestamp, $level, $message, $contextJson);

        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    public function info($message, $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    public function error($message, $context = []) {
        $this->log(self::ERROR, $message, $context);
    }

    public function debug($message, $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }
}
