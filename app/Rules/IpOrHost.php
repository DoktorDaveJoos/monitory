<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IpOrHost implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the value is a valid IP address (IPv4 or IPv6)
        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return;
        }

        // Check if the value is a valid hostname
        if ($this->isValidHost($value)) {
            return;
        }

        $fail($this->message());
    }

    /**
     * Validate if the given value is a valid hostname or hostname:port.
     */
    protected function isValidHost(string $value): bool
    {
        // Regular expression to validate hostname with optional port
        $pattern = '/^
            (?:(?:[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]\.)+[a-zA-Z]{2,63}|localhost)  # Valid hostnames or localhost
            (:(\d{1,5}))?$  # Optional port number (1-65535)
        $/x';

        if (preg_match($pattern, $value, $matches)) {
            if (isset($matches[2])) {
                // Extract port from matches
                $port = (int) $matches[2];

                return $port > 0 && $port <= 65535; // Port range validation (1-65535)
            }

            // If it's a Punycode domain, validate the Punycode structure
            if (str_starts_with($value, 'xn--')) {
                return $this->isValidPunycode($value);
            }

            // Check if the TLD is valid
            $array = explode('.', $value);
            $tld = array_pop($array);

            return $this->isValidTld($tld);
        }

        return false;
    }

    /**
     * Check if the Punycode is valid.
     */
    private function isValidPunycode(string $value): bool
    {
        $parts = explode('.', $value);

        foreach ($parts as $part) {
            // Ensure Punycode has valid structure after 'xn--'
            if (str_starts_with($part, 'xn--')) {
                $punycode = substr($part, 4); // Remove 'xn--'
                // Check if Punycode contains valid characters and doesn't have excessive dashes
                if (! preg_match('/^[a-zA-Z0-9-]+$/', $punycode) || preg_match('/-{2,}/', $punycode)) {
                    return false; // Invalid Punycode part
                }
            } else {
                // Check if each part of the hostname is a valid non-Punycode label
                if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]$/', $part)) {
                    return false; // Invalid non-Punycode part
                }
            }
        }

        return true;
    }

    /**
     * Validate TLD length.
     */
    private function isValidTld(string $tld): bool
    {
        // TLD must be between 2 and 63 characters long
        return strlen($tld) >= 2 && strlen($tld) <= 63;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The :attribute must be a valid IP address or hostname.';
    }
}
