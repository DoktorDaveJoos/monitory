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
            'valid ipv4 - Localhost' => ['127.0.0.1'],
            'valid ipv4 - Private Network' => ['192.168.1.1'],
            'valid ipv4 - Broadcast' => ['255.255.255.255'],
            'valid ipv4 - Class A' => ['10.0.0.1'],

            'valid ipv6 - Localhost' => ['::1'],
            'valid ipv6 - Global Unicast' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            'valid ipv6 - Link-Local' => ['fe80::1ff:fe23:4567:890a'],
            'valid ipv6 - ipv4-mapped' => ['::ffff:192.168.1.1'],

            'valid Hostname - Simple' => ['example.com'],
            'valid Hostname - Subdomain' => ['sub.example.com'],
            'valid Hostname - With Hyphen' => ['my-site.org'],
            'valid Hostname - Local' => ['myapp.local'],
            'valid Hostname - Multiple Hyphens' => ['host-with-dash.com'],

            'valid Localhost' => ['localhost'],

            'valid Domain with Port - Standard' => ['example.com:8080'],
            'valid Domain with Port - Subdomain' => ['subdomain.myapp.com:3000'],
        ];
    }

    /**
     * Data provider for invalid IP addresses and hostnames.
     */
    public static function invalidIpOrHostProvider(): array
    {
        return [
            'invalid ipv4 - Out of Range' => ['999.999.999.999'],
            'invalid ipv4 - All Octets Out of Range' => ['256.256.256.256'],
            'invalid ipv4 - Incomplete' => ['192.168.1'],
            'invalid ipv4 - Last Octet Out of Range' => ['10.0.0.999'],
            'invalid ipv4 - First Octet Too Long' => ['1234.123.123.123'],

            'invalid ipv6 - invalid Compression' => ['::12345'],
            'invalid ipv6 - Too Many Compressions' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334::'],
            'invalid ipv6 - Double Compression' => ['2001:db8::85a3::8a2e:370:7334'],
            'invalid ipv6 - invalid Segments' => ['1200::AB00:1234::2552:7777:1313'],

            'invalid Hostname - Double Dots' => ['example..com'],
            'invalid Hostname - Starts with Dash' => ['-example.com'],
            'invalid Hostname - Name Ends with Dash' => ['example-.com'],
            'invalid Hostname - Starts with Dot' => ['.example.com'],
            'invalid Hostname - Domain Ends with Dash' => ['example.com-'],
            'invalid Hostname - Contains Underscore' => ['exa_mple.com'],
            'invalid Hostname - Contains Special Character' => ['ex*ample.com'],
            'invalid Hostname - No TLD' => ['example'],
            'invalid Hostname - Only TLD' => ['com'],
            'invalid Hostname - TLD Too Short' => ['example.c'],

            'invalid Punycode - Incomplete' => ['xn--'],
            'invalid Punycode - Malformed' => ['xn--abc--def.com'],

            'invalid Localhost - Port Out of Range' => ['localhost:999999'],
            'invalid Localhost - Non-numeric Port' => ['localhost:abc'],

            'invalid Domain with Port - Out of Range' => ['example.com:70000'],
            'invalid Domain with Port - Non-numeric' => ['example.com:abcd'],
            'invalid Domain with Port - Missing Number' => ['example.com:'],
            'invalid Domain with Port - Negative Number' => ['subdomain.example.com:-1'],
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
