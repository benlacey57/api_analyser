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

    // Method to get average response time
    public function getAverageResponseTime() {
        $stmt = $this->pdo->query('SELECT AVG(responseTime) FROM api_metrics');
        return $stmt->fetchColumn();
    }

    // Method to get overall error rate
    public function getErrorRate() {
        $totalRequests = $this->getTotalRequestsCount();
        $errorRequests = $this->getErrorRequestsCount();

        return $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0;
    }

    // Method to get metrics summary for a specific period
    public function getMetricsSummary($startTime, $endTime) {
        $stmt = $this->pdo->prepare('SELECT AVG(responseTime) as avgResponseTime, COUNT(*) as totalRequests, SUM(case when statusCode >= 400 then 1 else 0 end) as errorRequests FROM api_metrics WHERE dateTime BETWEEN :startTime AND :endTime');
        $stmt->execute([':startTime' => $startTime, ':endTime' => $endTime]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result['errorRate'] = $result['totalRequests'] > 0 ? ($result['errorRequests'] / $result['totalRequests']) * 100 : 0;

        return $result;
    }
    
    public function getTotalRequestsCount() {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM api_metrics');
        return $stmt->fetchColumn();
    }

    public function getErrorRequestsCount() {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM api_metrics WHERE statusCode >= 400');
        return $stmt->fetchColumn();
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
