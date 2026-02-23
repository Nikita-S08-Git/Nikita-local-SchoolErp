<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Login Rate Limiting Test
 * 
 * Tests that the login endpoint is properly rate limited to 5 attempts per minute per IP.
 * This prevents brute force attacks on user accounts.
 */
class LoginRateLimitTest extends TestCase
{
    /**
     * Test that login rate limit blocks after 5 attempts
     * 
     * Makes 5 login attempts which should all go through (returning 401 for wrong password),
     * then makes a 6th attempt which should be rate limited (returning 429).
     */
    public function test_login_rate_limit_blocks_after_five_attempts(): void
    {
        // Make 5 requests - should all go through to the controller
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password'
            ]);
            
            // Should get 401 (unauthorized) not 429 (rate limited)
            // This means the request reached the controller
            $this->assertEquals(401, $response->status(), 
                "Attempt " . ($i + 1) . " should not be rate limited");
        }
        
        // 6th request should be rate limited
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);
        
        // Should get 429 (Too Many Requests)
        $response->assertStatus(429);
        $response->assertJsonStructure(['message']);
        
        // Verify the error message
        $this->assertStringContainsString('Too Many', $response->json('message'));
    }
    
    /**
     * Test that rate limit headers are present in responses
     * 
     * Verifies that the X-RateLimit-Limit and X-RateLimit-Remaining headers
     * are included in the response.
     */
    public function test_rate_limit_headers_are_present(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        // Check for rate limit headers
        $response->assertHeader('X-RateLimit-Limit', '5');
        $this->assertNotNull($response->headers->get('X-RateLimit-Remaining'));
    }
    
    /**
     * Test that rate limit remaining decrements with each attempt
     * 
     * Verifies that the X-RateLimit-Remaining header decrements from 4 to 0
     * as attempts are made.
     */
    public function test_rate_limit_remaining_decrements(): void
    {
        // First attempt - should have 4 remaining (5 total - 1 used)
        $response1 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $remaining1 = $response1->headers->get('X-RateLimit-Remaining');
        $this->assertEquals(4, $remaining1);
        
        // Second attempt - should have 3 remaining
        $response2 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $remaining2 = $response2->headers->get('X-RateLimit-Remaining');
        $this->assertEquals(3, $remaining2);
        
        // Third attempt - should have 2 remaining
        $response3 = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $remaining3 = $response3->headers->get('X-RateLimit-Remaining');
        $this->assertEquals(2, $remaining3);
    }
    
    /**
     * Test that Retry-After header is present when rate limited
     * 
     * Verifies that when the rate limit is exceeded, the response includes
     * a Retry-After header telling the client when they can try again.
     */
    public function test_retry_after_header_present_when_rate_limited(): void
    {
        // Make 5 requests to exhaust the rate limit
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password'
            ]);
        }
        
        // 6th request should be rate limited and include Retry-After header
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $response->assertStatus(429);
        $this->assertNotNull($response->headers->get('Retry-After'));
        
        // Retry-After should be a positive integer (seconds)
        $retryAfter = $response->headers->get('Retry-After');
        $this->assertIsNumeric($retryAfter);
        $this->assertGreaterThan(0, $retryAfter);
    }
}
