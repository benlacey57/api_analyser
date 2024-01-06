<?php
require_once __FILE__ . '/classes/api_analyser.php';

$tester = new ApiPerformanceTester('');
$results = $tester->analyzeApiResponse('https://swapi.dev/api/people/');

// Print results or log them
print_r($results);
