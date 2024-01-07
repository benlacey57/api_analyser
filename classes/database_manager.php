<?php
// Include necessary libraries
require 'vendor/autoload.php';

use Dotenv\Dotenv;

class DatabaseManager {
    private $pdo;

    /**
     * Constructor for the DatabaseManager
     * @param string $dbPath Path to the SQLite database file
     */
    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $dbPath = $_ENV['DB_PATH'];
        $this->connect($dbPath);
    }

    /**
     * Connects to the SQLite database
     * @param string $dbPath Path to the database file
     */
    private function connect($dbPath) {
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Initializes tables if they don't exist
     */
    private function initializeTables() {
        // Create tables based on schema
        $commands = [
            // API Metrics Table
            "CREATE TABLE IF NOT EXISTS api_metrics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                dateTime TEXT NOT NULL,
                url TEXT NOT NULL,
                responseTime REAL,
                statusCode INTEGER,
                allowedMethods TEXT,
                protocol TEXT,
                rateLimit INTEGER,
                rateRemaining INTEGER,
                rateReset TEXT,
                errorRate REAL
            )",

            // Log Data Table
            "CREATE TABLE IF NOT EXISTS log_data (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                dateTime TEXT NOT NULL,
                level TEXT,
                message TEXT,
                context TEXT
            )",

            // Issues Identified Table
            "CREATE TABLE IF NOT EXISTS identified_issues (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                dateTime TEXT NOT NULL,
                description TEXT,
                url TEXT,
                additionalInfo TEXT
            )",

            "CREATE TABLE k6_test_data (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                metric_name TEXT,
                metric_value REAL,
                test_timestamp DATETIME,
                tags TEXT,
                test_type TEXT,  -- Type of test (spike, soak, stress, etc.)
                test_id TEXT     -- Unique identifier for a k6 test run
            )",
            // ... Add other table initialization as necessary ...
        ];

        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }

    /**
     * Inserts API metric data into the database
     * @param array $data Data to be inserted
     */
    public function insertApiMetric($data) {
        $stmt = $this->pdo->prepare('INSERT INTO api_metrics (dateTime, url, responseTime, statusCode, allowedMethods, protocol, rateLimit, rateRemaining, rateReset, errorRate) VALUES (:dateTime, :url, :responseTime, :statusCode, :allowedMethods, :protocol, :rateLimit, :rateRemaining, :rateReset, :errorRate)');
        $stmt->execute($data);
    }

    /**
     * Inserts log data into the database
     * @param array $data Data to be inserted
     */
    public function insertLogData($data) {
        $stmt = $this->pdo->prepare('INSERT INTO log_data (dateTime, level, message, context) VALUES (:dateTime, :level, :message, :context)');
        $stmt->execute($data);
    }

    /**
     * Inserts identified issue data into the database
     * @param array $data Data to be inserted
     */
    public function insertIdentifiedIssue($data) {
        $stmt = $this->pdo->prepare('INSERT INTO identified_issues (dateTime, description, url, additionalInfo) VALUES (:dateTime, :description, :url, :additionalInfo)');
        $stmt->execute($data);
    }

    /**
     * Imports k6 test data from a JSON file into the database
     * @param string $jsonFilePath Path to the k6 JSON output file
     */
    public function importK6Data($jsonFilePath, $testType, $testId) {
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        foreach ($jsonData as $entry) {
            // Extract metric data from entry (customize based on actual JSON structure)
            $metricName = $entry['metric']['name'] ?? null;
            $metricValue = $entry['data']['value'] ?? null;
            $testTimestamp = $entry['data']['timestamp'] ?? null;
        
            // Insert data into the k6_test_data table
            $stmt = $this->pdo->prepare("INSERT INTO k6_test_data (metric_name, metric_value, test_timestamp, test_type, test_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$metricName, $metricValue, $testTimestamp, $testType, $testId]);
        }
    }
    
    // Add methods for aggregating and retrieving data as necessary
}

// Usage
// $dbManager = new DatabaseManager('/path/to/database.db');
