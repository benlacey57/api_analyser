<?php

class IssueDetector {
    private $logFilePath;
    private $issues = [];

    public function __construct($logFilePath) {
        $this->logFilePath = $logFilePath;
    }

    public function analyzeLogs() {
        $logEntries = file($this->logFilePath);

        foreach ($logEntries as $entry) {
            $data = json_decode($entry, true);
            $this->detectIssues($data);
        }
        
        return $this->issues;
    }

    private function detectIssues($logEntry) {
        // Example: Detect high response times
        if (isset($logEntry['responseTime']) && $logEntry['responseTime'] > 1) { // 1 second threshold
            $this->issues[] = "High response time detected: " . $logEntry['responseTime'] . " for URL " . $logEntry['context']['url'];
        }

        // Example: Detect frequent errors
        if (isset($logEntry['level']) && $logEntry['level'] == 'ERROR') {
            $this->issues[] = "Error detected: " . $logEntry['message'];
        }

        // Add more detection logic as needed...
    }
}
