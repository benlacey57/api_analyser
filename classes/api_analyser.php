<?php

# $analyzer = new ApiAnalyzer($dbManager, $logger);
# // Define a custom test function
# $customTestFunction = function($response) {
#    // Custom logic to evaluate the response
#    // For example, check if a specific field is present in JSON response:
#    $body = json_decode($response->getBody(), true);
#    return isset($body['expectedField']);
#};

// Run the custom test
$analyzer->runCustomTest('https://api.example.com/endpoint', $customTestFunction);


require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RetryMiddleware;

class ApiPerformanceTester {
    private $client;
    private $dbManager;
    private $logger;

    public function __construct($dbManager, $logger) {
        $stack = HandlerStack::create();
        $stack->push($this->createRetryMiddleware());
        
        $this->client = new Client(['handler' => $stack, 'http_errors' => false]);
        $this->dbManager = $dbManager;
        $this->logger = $logger;
    }

    private function createRetryMiddleware() {
        return Middleware::retry(
            function($retry, $request, $response, $exception) {
                // Retry conditions: response is null (network issue) or status code is 5xx (server errors)
                if ($response && $response->getStatusCode() < 500) {
                    return false;
                }
                if ($retry >= 3) { // Limit the number of retries to 3
                    return false;
                }
                return true;
            },
            function($retry) {
                return 1000 * $retry; // Wait time between retries (ms)
            }
        );
    }

    public function performConcurrencyTest($urls) {
        $promises = [];

        foreach ($urls as $url) {
            $promises[$url] = $this->client->getAsync($url)
                ->then(
                    function ($response) use ($url) {
                        $this->handleSuccessResponse($url, $response);
                    },
                    function ($exception) use ($url) {
                        $this->handleErrorResponse($url, $exception);
                    }
                );
        }

        // Initiate the concurrent requests and wait for all to complete
        $results = Promise\settle($promises)->wait();
        $this->logger->logInfo("Concurrency Test Completed");
    }

    private function handleSuccessResponse($url, $response) {
        // Extract necessary data from $response and log or store it
        $statusCode = $response->getStatusCode();
        // ...other data extraction...

        // Store data into database
        $this->dbManager->insertApiMetric(/* ...data... */);

        // Log info about the successful request
        $this->logger->logInfo("Request to $url successful with status $statusCode");
    }

    private function handleErrorResponse($url, $exception) {
        // Log error details
        $this->logger->logError("Request to $url failed.", $exception);
    }

    public function analyzeApiResponse($url) {
        try {
            $startTime = microtime(true);

            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiCredentials,
                    // ... other headers
                ]
            ]);

            $endTime = microtime(true);

            $headers = $response->getHeaders();
            $bodySize = $response->getBody()->getSize();
            $statusCode = $response->getStatusCode();

            // Analysis
            $compression = $headers['Content-Encoding'][0] ?? 'none';
            $cacheControl = $headers['Cache-Control'][0] ?? 'no-cache';
            $eTag = $headers['ETag'][0] ?? 'not set';

            return [
                'responseTime' => $endTime - $startTime,
                'statusCode' => $statusCode,
                'bodySize' => $bodySize,
                'compression' => $compression,
                'cacheControl' => $cacheControl,
                'eTag' => $eTag,
            ];
        } catch (GuzzleException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function performLoadTest($urls) {
        $promises = [];
        foreach ($urls as $url) {
            $promises[] = $this->client->getAsync($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiCredentials,
                ]
            ]);
        }

        // Wait for all the requests to complete; throws a ConnectException
        // if any of the requests fail
        $results = Promise\unwrap($promises);

        // Analyze results
        // ... Implement analysis based on response times, error rates, etc.
    }

    // Method to run a custom test script
    public function runCustomTest($url, callable $testFunction) {
        try {
            $response = $this->client->request('GET', $url); // Or other methods as needed
            $result = $testFunction($response); // Execute the custom test function

            // Log or process the result of the custom test
            $this->logger->info("Custom test completed", ['url' => $url, 'result' => $result]);

            return $result;
        } catch (Exception $e) {
            $this->logger->error("Custom test failed", ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
