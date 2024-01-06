<?php

class DatabaseManager {
    private $pdo;

    public function __construct($dbPath) {
        $this->connect($dbPath);
        $this->initializeTables();
    }

    private function connect($dbPath) {
        $this->pdo = new PDO("sqlite:" . $dbPath);
    }

    private function initializeTables() {
        $commands = [
            'CREATE TABLE IF NOT EXISTS api_metrics (
                id INTEGER PRIMARY KEY,
                dateTime TEXT,
                url TEXT,
                responseTime REAL,
                statusCode INTEGER,
                errorRate REAL,
            )',
            //... other table definitions
        ];
        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }

    public function insertApiMetric($metric) {
        $stmt = $this->pdo->prepare('INSERT INTO api_metrics (dateTime, url, responseTime, statusCode, errorRate, ...) VALUES (:dateTime, :url, :responseTime, :statusCode, :errorRate, ...)');

        // Bind values
        $stmt->bindValue(':dateTime', $metric['dateTime']);
        $stmt->bindValue(':url', $metric['url']);
        $stmt->bindValue(':responseTime', $metric['responseTime']);
        $stmt->bindValue(':statusCode', $metric['statusCode']);
        $stmt->bindValue(':errorRate', $metric['errorRate']);
        //... bind other fields

        $stmt->execute();
    }

    // Add more database interaction methods as needed
}
