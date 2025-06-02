<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class IpRuleTest extends TestCase
{
    /**
     * Test that the ip rule passes with valid IP addresses
     */
    public function testIpRulePasses()
    {
        // IPv4
        $validator = new Validator(['ip' => '192.168.1.1'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // IPv4 with zeroes
        $validator = new Validator(['ip' => '127.0.0.1'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // IPv4 max values
        $validator = new Validator(['ip' => '255.255.255.255'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // IPv6 full
        $validator = new Validator(['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // IPv6 compressed
        $validator = new Validator(['ip' => '2001:db8:85a3::8a2e:370:7334'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // IPv6 loopback
        $validator = new Validator(['ip' => '::1'], ['ip' => 'ip']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['ip' => null], ['ip' => 'nullable|ip']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the ip rule fails with invalid IP addresses
     */
    public function testIpRuleFails()
    {
        // Invalid IPv4 (too many segments)
        $validator = new Validator(['ip' => '192.168.1.1.5'], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Invalid IPv4 (out of range)
        $validator = new Validator(['ip' => '192.168.1.256'], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Invalid IPv4 (wrong format)
        $validator = new Validator(['ip' => '192.168.1'], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Invalid IPv6 (invalid characters)
        $validator = new Validator(['ip' => '2001:0xz8:85a3:0000:0000:8a2e:0370:7334'], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Plain text
        $validator = new Validator(['ip' => 'not an ip'], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['ip' => ''], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['ip' => ['192.168.1.1']], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());

        // Number
        $validator = new Validator(['ip' => 123456], ['ip' => 'ip']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testIpErrorMessage()
    {
        $validator = new Validator(['ip' => 'not an ip'], ['ip' => 'ip']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('ip', $errors);
        $this->assertStringContainsString('ip must be a valid IP address', $errors['ip'][0]);
    }
}
