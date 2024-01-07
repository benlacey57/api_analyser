// test_script.js
import http from 'k6/http';
import { check } from 'k6';

export let options = {
    stages: [
        // Define stages of virtual users and duration
        { duration: '1m', target: 10 }, // Ramp up to 10 users over 1 minute
        { duration: '3m', target: 10 }, // Stay at 10 users for 3 minutes
        { duration: '1m', target: 0 },  // Ramp down to 0 users over 1 minute
    ],
};

export default function () {
    let response = http.get('http://your.api.endpoint/');

    check(response, {
        'is status 200': (r) => r.status === 200,
        // Add more checks as necessary
    });
}
