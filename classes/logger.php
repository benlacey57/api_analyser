<?php

class Logger {
    private $logFilePath;

    public function __construct($logFilePath) {
        $this->logFilePath = $logFilePath;
    }

    public function logInfo($message) {
        $this->logToFile("INFO: " . $message);
    }

    public function logError($message, $error = null) {
        $logMessage = "ERROR: " . $message;
        if ($error) {
            $logMessage .= " | Details: " . $error->getMessage();
        }
        $this->logToFile($logMessage);
    }

    private function logToFile($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = $timestamp . " " . $message . "\n";
        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
