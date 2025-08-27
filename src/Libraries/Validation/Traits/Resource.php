<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.8
 */

namespace Quantum\Libraries\Validation\Traits;

/**
 * Trait Resource
 * @package Quantum\Libraries\Validation\Rules
 */
trait Resource
{

    /**
     * Checks for valid URL or subdomain
     * @param string $value
     * @return bool
     */
    protected function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Checks to see if the url exists
     * @param string $value
     * @return bool
     */
    protected function urlExists(string $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $host = parse_url(strtolower($value), PHP_URL_HOST);

        if (!$host) {
            return false;
        }

        if (function_exists('checkdnsrr') && function_exists('idn_to_ascii')) {
            return checkdnsrr(idn_to_ascii($host, 0, INTL_IDNA_VARIANT_UTS46), 'A');
        } else {
            return gethostbyname($host) !== $host;
        }
    }

    /**
     * Checks for valid IP address
     * @param string $value
     * @return bool
     */
    protected function ip(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Checks for valid IPv4 address
     * @param string $value
     * @return bool
     */
    protected function ipv4(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Checks for valid IPv6 address
     * @param string $value
     * @return bool
     */
    protected function ipv6(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }
}