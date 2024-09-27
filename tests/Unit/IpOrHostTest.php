<?php

namespace Tests\Unit;

use App\Rules\IpOrHost;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IpOrHostTest extends TestCase
{
    /**
     * Data provider for valid IP addresses and hostnames.
     */
    public static function validIpOrHostProvider(): array
    {
        return [
            // Valid IPv4 addresses
            ['127.0.0.1'],
            ['192.168.1.1'],
            ['255.255.255.255'],
            ['10.0.0.1'],

            // Valid IPv6 addresses
            ['::1'],
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            ['fe80::1ff:fe23:4567:890a'],
            ['::ffff:192.168.1.1'],

            // Valid hostnames
            ['example.com'],
            ['sub.example.com'],
            ['my-site.org'],
            ['myapp.local'],
            ['host-with-dash.com'],

            // Valid localhost
            ['localhost'],

            // Valid domain with port (if necessary)
            ['example.com:8080'],
            ['subdomain.myapp.com:3000'],
        ];
    }

    /**
     * Data provider for invalid IP addresses and hostnames.
     */
    public static function invalidIpOrHostProvider(): array
    {
        return [
            // Invalid IPv4 addresses
            ['999.999.999.999'], // Out of range
            ['256.256.256.256'], // Out of range
            ['192.168.1'],       // Incomplete IPv4
            ['10.0.0.999'],      // Invalid last octet
            ['1234.123.123.123'], // Too many digits in first octet

            // Invalid IPv6 addresses
            ['::12345'],         // Invalid compression or too many digits
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334::'], // Too many ::
            ['2001:db8::85a3::8a2e:370:7334'], // Double ::
            ['1200::AB00:1234::2552:7777:1313'], // Invalid segments

            // Invalid hostnames
            ['example..com'],        // Double dots
            ['-example.com'],        // Starts with a dash
            ['example-.com'],        // Ends with a dash
            ['.example.com'],        // Starts with a dot
            ['example.com-'],        // Ends with a dash
            ['exa_mple.com'],        // Underscore in hostname (invalid)
            ['ex*ample.com'],        // Special character
            ['example'],             // No TLD
            ['com'],                 // Only TLD
            ['example.c'],           // TLD too short

            // Invalid Punycode/Unicode domains
            ['xn--'], // Incomplete punycode
            ['xn--abc--def.com'], // Malformed punycode

            // Invalid localhost with port
            ['localhost:999999'], // Invalid port number
            ['localhost:abc'], // Non-numeric port

            // Invalid domain with port
            ['example.com:70000'],  // Port out of range (valid range 1-65535)
            ['example.com:abcd'],   // Non-numeric port
            ['example.com:'],       // Missing port number after colon
            ['subdomain.example.com:-1'],  // Negative port number
        ];
    }

    /**
     * Example test that validates IP or host using the data provider.
     */
    #[DataProvider('validIpOrHostProvider')]
    public function test_valid_options($input)
    {
        $validator = Validator::make(
            ['host' => $input],
            ['host' => new IpOrHost]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Example test that validates invalid IP or host using the data provider.
     */
    #[DataProvider('invalidIpOrHostProvider')]
    public function test_invalid_options($input)
    {
        $validator = Validator::make(
            ['host' => $input],
            ['host' => new IpOrHost]
        );

        $this->assertFalse($validator->passes());
    }
}
