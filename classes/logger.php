<?php

enum LogLevel: string {
    case INFO = 'INFO';
    case ERROR = 'ERROR';
    case DEBUG = 'DEBUG';
}

class Logger {
    private $logFilePath;

    /**
     * Constructor for the Logger class
     * @param string $logFilePath Path to the log file
     */
    public function __construct($logFilePath) {
        $this->logFilePath = $logFilePath;
    }

    /**
     * Logs a message with a specific level to the log file
     * @param LogLevel $level The level of the log entry
     * @param string $message The message to log
     * @param array $context Additional context to log with the message
     */
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

    /**
     * Logs an informational message
     * @param string $message The message to log
     * @param array $context Additional context to log with the message
     */
    public function info($message, $context = []) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Logs an error message
     * @param string $message The message to log
     * @param array $context Additional context to log with the message
     */
    public function error($message, $context = []) {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Logs a debug message
     * @param string $message The message to log
     * @param array $context Additional context to log with the message
     */
    public function debug($message, $context = []) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}

// Usage
// $logger = new Logger('/path/to/log.txt');
// $logger->info("Informational message");
