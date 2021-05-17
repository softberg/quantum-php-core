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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Validation\Rules;

/**
 * Trait Resource
 * @package Quantum\Libraries\Validation\Rules
 */
trait Resource
{

    /**
     * Checks for valid URL or subdomain
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function url(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                $this->addError($field, 'url', $param);
            }
        }
    }

    /**
     * Checks to see if the url exists
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function urlExists(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
                $url = parse_url(strtolower($value));

                if (isset($url['host'])) {
                    $url = $url['host'];
                }

                if (function_exists('checkdnsrr') && function_exists('idn_to_ascii')) {
                    if (checkdnsrr(idn_to_ascii($url, 0, INTL_IDNA_VARIANT_UTS46), 'A') === false) {
                        $error = true;
                    }
                } else {
                    if (gethostbyname($url) == $url) {
                        $error = true;
                    }
                }

                if ($error) {
                    $this->addError($field, 'urlExists', $param);
                }
            } else {
                $this->addError($field, 'url', $param);
            }
        }
    }

    /**
     * Checks for valid IP address
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function ip(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_IP) === false) {
                $this->addError($field, 'ip', $param);
            }
        }
    }

    /**
     * Checks for valid IPv4 address
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function ipv4(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
                $this->addError($field, 'ipv4', $param);
            }
        }
    }

    /**
     * Check sfor valid IPv6 address
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function ipv6(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                $this->addError($field, 'ipv6', $param);
            }
        }
    }

}