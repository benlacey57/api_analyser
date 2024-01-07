-- API Metrics Table
CREATE TABLE api_metrics (
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
);

-- Log Data Table
CREATE TABLE log_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dateTime TEXT NOT NULL,
    level TEXT,
    message TEXT,
    context TEXT
);

-- Issues Identified Table
CREATE TABLE identified_issues (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dateTime TEXT NOT NULL,
    description TEXT,
    url TEXT,
    additionalInfo TEXT
);

CREATE TABLE k6_test_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    metric_name TEXT,
    metric_value REAL,
    test_timestamp DATETIME,
    tags TEXT,
    test_type TEXT,  -- Type of test (spike, soak, stress, etc.)
    test_id TEXT     -- Unique identifier for a k6 test run
);

-- Indexes
CREATE INDEX idx_api_metrics_url ON api_metrics(url);
CREATE INDEX idx_api_metrics_statusCode ON api_metrics(statusCode);
CREATE INDEX idx_api_metrics_dateTime ON api_metrics(dateTime);

CREATE INDEX idx_log_data_level ON log_data(level);
CREATE INDEX idx_log_data_dateTime ON log_data(dateTime);

CREATE INDEX idx_identified_issues_dateTime ON identified_issues(dateTime);
