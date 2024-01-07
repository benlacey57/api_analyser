# API Performance Analyzer

## Overview

The API Performance Analyser is a versatile tool designed to test and analyse the performance of APIs under various conditions. It allows you to conduct tests, store test data in an SQLite database, and perform in-depth analysis to gain insights into your API's behavior.

### PHP Classes
There are a series of PHP Classes that are responsible for reviewing the API endpoints, analysing the headers and storing data for key metrics. You can define your own custom tests that can be run and you are able to use various databases for the data storage.

### JavaScript K6 Library
For API load testing and stress testing we can use K6 to measure performance with various conditions.

Developed By: Ben Lacey
Website: https://benlacey.co.uk
Version: 1.2
Date Created: 6th January 2024


## Features

- **Flexible Testing**: Easily conduct performance tests on APIs with support for different types of tests (e.g., spike, soak, stress).
- **Detailed Data Storage**: Store a wide range of performance metrics, including response times, status codes, payload sizes, and more.
- **Test Identification**: Assign a unique test ID and specify the type of test being conducted for easy categorization and comparison.
- **Custom Test Cases**: Define and execute custom test cases to target specific functionality, regression tests, or compliance checks.
- **Security Testing**: Incorporate security scanning and testing to identify vulnerabilities like unsecured endpoints and improper authentication handling.
- **Resilient Testing**: Implement error handling strategies with retry logic and circuit breakers to enhance the tool's resilience.
- **Database Persistence**: Store test data in an SQLite database for historical analysis and long-term tracking.

## Installation

1. Clone this repository to your local machine:

```
git clone https://github.com/yourusername/api-performance-analyzer.git
```

Install dependencies using Composer:
```composer install```

Create an .env file in the project root and define your database credentials.

```
DB_PATH=/path/to/your/database.db
```


## Running API Tests Using K6
Write your API performance test script using k6 and generate JSON output (e.g., test_script.js):

```
// Example k6 test_script.js
import http from 'k6/http';
import { check } from 'k6';

export default function () {
    // Your test logic here
}
```

### Run the test and generate JSON output

```k6 run --out json=test_results.json test_script.js```


## Importing K6 Test Data To Database
Create a DatabaseManager instance and use it to import test data into your SQLite database:

```
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use DatabaseManager;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create DatabaseManager instance
$dbManager = new DatabaseManager();

// Import k6 test data (provide test type, test ID, user count, duration, and script name)

$dbManager->importK6Data('/k6/test_results.json', 'spike', 'unique_test_id_123', 100, 3600, 'testScript.js');
```

## Analysing Test Data
Analyse test data by querying the SQLite database. Example queries for analysis:

-- Get response times for a specific test type (e.g., 'spike')
SELECT metric_value, test_timestamp FROM k6_test_data WHERE test_type = 'spike' AND metric_name = 'http_req_duration';

-- Get error rates for all tests
SELECT COUNT(*) AS error_count, test_id FROM k6_test_data WHERE metric_name = 'http_req_failed' GROUP BY test_id;
Perform custom analyses based on your specific requirements and goals.

### Code Considerations
Flexibility: The tool is designed to be flexible and customizable. Modify the DatabaseManager class to fit your database schema and adapt the k6 test script structure to match your testing needs.

Error Handling: Implement robust error handling and logging within the DatabaseManager class to handle issues with file reading, database inserts, and data processing.

Security: Ensure sensitive data such as database credentials and test IDs are stored securely and not exposed in public repositories.

Testing: Test your k6 scripts thoroughly before running them on production APIs. Use test data from a staging environment if possible.

### Contributing
Contributions are welcome! Feel free to open issues or pull requests to suggest improvements, report bugs, or share your ideas for enhancing the tool.
