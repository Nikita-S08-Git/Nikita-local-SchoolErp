<?php

/**
 * Login Rate Limiting Test Script
 * 
 * This script tests the login rate limiting functionality
 * by making multiple login attempts and checking the responses.
 */

$baseUrl = 'http://127.0.0.1:8000/api/login';
$testData = [
    'email' => 'test@example.com',
    'password' => 'wrongpassword'
];

echo "===========================================\n";
echo "Login Rate Limiting Test\n";
echo "===========================================\n\n";

echo "Testing login endpoint: $baseUrl\n";
echo "Rate limit: 5 attempts per minute per IP\n\n";

// Function to make a login request
function makeLoginRequest($url, $data, $attemptNumber) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    // Extract rate limit headers
    $rateLimitLimit = null;
    $rateLimitRemaining = null;
    $retryAfter = null;
    
    if (preg_match('/X-RateLimit-Limit:\s*(\d+)/i', $headers, $matches)) {
        $rateLimitLimit = $matches[1];
    }
    if (preg_match('/X-RateLimit-Remaining:\s*(\d+)/i', $headers, $matches)) {
        $rateLimitRemaining = $matches[1];
    }
    if (preg_match('/Retry-After:\s*(\d+)/i', $headers, $matches)) {
        $retryAfter = $matches[1];
    }
    
    return [
        'attempt' => $attemptNumber,
        'status' => $httpCode,
        'body' => json_decode($body, true),
        'rate_limit_limit' => $rateLimitLimit,
        'rate_limit_remaining' => $rateLimitRemaining,
        'retry_after' => $retryAfter
    ];
}

// Make 7 login attempts
echo "Making 7 login attempts...\n\n";

for ($i = 1; $i <= 7; $i++) {
    echo "Attempt #$i:\n";
    echo str_repeat('-', 50) . "\n";
    
    $result = makeLoginRequest($baseUrl, $testData, $i);
    
    echo "  HTTP Status: {$result['status']}\n";
    
    if ($result['rate_limit_limit']) {
        echo "  X-RateLimit-Limit: {$result['rate_limit_limit']}\n";
    }
    
    if ($result['rate_limit_remaining'] !== null) {
        echo "  X-RateLimit-Remaining: {$result['rate_limit_remaining']}\n";
    }
    
    if ($result['retry_after']) {
        echo "  Retry-After: {$result['retry_after']} seconds\n";
    }
    
    if ($result['body']) {
        echo "  Response: " . json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
    }
    
    // Check if rate limited
    if ($result['status'] == 429) {
        echo "  ✓ RATE LIMITED (Expected after 5 attempts)\n";
    } elseif ($result['status'] == 401) {
        echo "  ✓ Unauthorized (Expected - wrong password)\n";
    } elseif ($result['status'] == 200) {
        echo "  ✓ Success (Login successful)\n";
    } else {
        echo "  ✗ Unexpected status code\n";
    }
    
    echo "\n";
    
    // Small delay between requests
    usleep(100000); // 0.1 second
}

echo "===========================================\n";
echo "Test Summary\n";
echo "===========================================\n\n";

echo "Expected behavior:\n";
echo "  - Attempts 1-5: Should return 401 (Unauthorized) or 200 (Success)\n";
echo "  - Attempts 6-7: Should return 429 (Too Many Requests)\n";
echo "  - Rate limit headers should be present in all responses\n";
echo "  - Retry-After header should be present in 429 responses\n\n";

echo "If you see 429 status on attempts 6-7, rate limiting is working! ✓\n";
echo "Wait 60 seconds and run again to verify rate limit resets.\n\n";
