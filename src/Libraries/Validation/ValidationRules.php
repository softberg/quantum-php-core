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

namespace Quantum\Libraries\Validation;

use Quantum\Factory\ModelFactory;
use Quantum\Libraries\Upload\File;
use Quantum\Di\Di;

/**
 * Trait ValidationRules
 * @package Quantum\Libraries\Validation
 */
trait ValidationRules
{

    /**
     * Checks Field Required
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function required(string $field, string $value, $param = null)
    {
        if ($value == false || $value === '0' || empty($value)) {
            $this->addError($field, 'required', $param);
        }
    }

    /**
     * Checks Email
     * @param string $field
     * @param string $value
     * @param null $param
     */
    protected function email(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, 'email', $param);
            }
        }
    }

    /**
     * Checks the min Length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function minLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) < (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) < (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'minLen', $param);
            }
        }
    }

    /**
     * Checks the max Length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function maxLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) > (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) > (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'maxLen', $param);
            }
        }

    }

    /**
     * Checks the exact length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function exactLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) !== (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) !== (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'exactLen', $param);
            }
        }

    }

    /**
     * Checks the alpha characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alpha(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 0) {
                $this->addError($field, 'alpha', $param);
            }
        }
    }

    /**
     * Checks the alpha and numeric characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 0) {
                $this->addError($field, 'alphaNumeric', $param);
            }
        }
    }

    /**
     * Checks the alpha and dash characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaDash(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $value) === 0) {
                $this->addError($field, 'alphaDash', $param);
            }
        }
    }

    /**
     * Checks the alpha numeric and space characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaSpace(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $value) === 0) {
                $this->addError($field, 'alphaSpace', $param);
            }
        }


    }

    /**
     * Checks the numeric value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function numeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value)) {
                $this->addError($field, 'numeric', $param);
            }
        }
    }

    /**
     * Checks the integer value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function integer(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $this->addError($field, 'integer', $param);
            }
        }
    }

    /**
     * Checks the float value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function float(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                $this->addError($field, 'float', $param);
            }
        }
    }

    /**
     * Checks the boolean value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function boolean(string $field, string $value, $param = null)
    {

        if (!empty($value) && $value !== 0) {
            $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

            if (!in_array($value, $booleans, true)) {
                $this->addError($field, 'boolean', $param);
            }
        }
    }

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

    /**
     * Checks for a valid credit card number
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function creditCard(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            $number = preg_replace('/\D/', '', $value);

            if (function_exists('mb_strlen')) {
                $number_length = mb_strlen($number);
            } else {
                $number_length = strlen($number);
            }

            if ($number_length == 0) {
                $this->addError($field, 'creditCard', $param);
            }

            $parity = $number_length % 2;

            $total = 0;

            for ($i = 0; $i < $number_length; ++$i) {
                $digit = $number[$i];

                if ($i % 2 == $parity) {
                    $digit *= 2;

                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }

                $total += $digit;
            }

            if ($total % 10 != 0) {
                $this->addError($field, 'creditCard', $param);
            }
        }
    }

    /**
     * Checks for a valid format human name
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function name(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match("/^([a-z \p{L} '-])+$/i", $value) === 0) {
                $this->addError($field, 'name', $param);
            }
        }
    }

    /**
     * Verifies that a value is contained within the pre-defined value set
     * @param string $field
     * @param string $value
     * @param string $param
     */
    protected function contains(string $field, string $value, string $param)
    {
        if (!empty($value)) {
            $param = trim(strtolower($param));
            $value = trim(strtolower($value));

            if (preg_match_all('#\'(.+?)\'#', $param, $matches, PREG_PATTERN_ORDER)) {
                $param = $matches[1];
            } else {
                $param = explode(chr(32), $param);
            }

            if (!in_array($value, $param)) {
                $this->addError($field, 'contains', null);
            }
        }
    }

    /**
     * Verifies that a value is contained within the pre-defined value set.
     * @param string $field
     * @param string $value
     * @param array $param
     */
    protected function containsList(string $field, string $value, array $param)
    {
        if (!empty($value)) {
            $param = array_map(function ($param) {
                return trim(strtolower($param));
            }, $param);

            $value = trim(strtolower($value));

            if (!in_array($value, $param)) {
                $this->addError($field, 'containsList', 'null');
            }
        }
    }

    /**
     * Verifies that a value is not contained within the pre-defined value set.
     * @param string $field
     * @param string $value
     * @param array $param
     */
    protected function doesntContainsList(string $field, string $value, array $param)
    {
        if (!empty($value)) {
            $param = array_map(function ($param) {
                return trim(strtolower($param));
            }, $param);

            $value = trim(strtolower($value));

            if (in_array($value, $param)) {
                $this->addError($field, 'doesntContainsList', null);
            }
        }
    }

    /**
     * Checks that the provided string is a likely street address.
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function streetAddress(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            $hasLetter = preg_match('/[a-zA-Z]/', $value);
            $hasDigit = preg_match('/\d/', $value);
            $hasSpace = preg_match('/\s/', $value);

            $passes = $hasLetter && $hasDigit && $hasSpace;

            if (!$passes) {
                $this->addError($field, 'streetAddress', $param);
            }
        }
    }

    /**
     * Checks for a valid IBAN
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function iban(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            static $character = [
                'A' => 10, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
                'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22,
                'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28,
                'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
                'Z' => 35, 'B' => 11,
            ];

            if (!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $value)) {
                $this->addError($field, 'iban', $param);
            }

            $iban = str_replace(' ', '', $value);
            $iban = substr($iban, 4) . substr($iban, 0, 4);
            $iban = strtr($iban, $character);

            if (bcmod($iban, 97) != 1) {
                $this->addError($field, 'iban', $param);
            }
        }


    }

    /**
     * Determines if the provided numeric value is lower to a specific value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function minNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value) || !is_numeric($param) || ($value < $param)) {
                $this->addError($field, 'minNumeric', $param);
            }
        }
    }

    /**
     * Determines if the provided numeric value is higher to a specific value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function maxNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value) || !is_numeric($param) || ($value > $param)) {
                $this->addError($field, 'maxNumeric', $param);
            }
        }
    }

    /**
     * Determines if the provided input is a valid date
     * @param string $field
     * @param string $value
     * @param string|null $param format
     */
    protected function date(string $field, string $value, string $param = null)
    {
        if (!empty($value)) {
            if (!$param) {
                $cdate1 = date('Y-m-d', strtotime($value));
                $cdate2 = date('Y-m-d H:i:s', strtotime($value));

                if ($cdate1 != $value && $cdate2 != $value) {
                    $this->addError($field, 'date', $param);
                }
            } else {
                $date = \DateTime::createFromFormat($param, $value);

                if ($date === false || $value != date($param, $date->getTimestamp())) {
                    $this->addError($field, 'date', $param);
                }
            }
        }
    }

    /**
     * Ensures the value starts with a certain character / set of character
     * @param string $field
     * @param string $value
     * @param string $param
     */
    protected function starts(string $field, string $value, string $param)
    {
        if (!empty($value)) {
            if (strpos($value, $param) !== 0) {
                $this->addError($field, 'starts', $param);
            }
        }
    }

    /**
     * Validates the phone number // 555-555-5555 , 5555425555, 555 555 5555, 1(519) 555-4444, 1 (519) 555-4422, +1-555-555-5555
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function phoneNumber(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            $regex = '/^(\+*\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i';

            if (!preg_match($regex, $value)) {
                $this->addError($field, 'phoneNumber', $param);
            }
        }
    }

    /**
     * Custom regex validator
     * @param string $field
     * @param string $value
     * @param string $param
     */
    protected function regex(string $field, string $value, string $param)
    {
        if (!empty($value)) {
            if (!preg_match($param, $value)) {
                $this->addError($field, 'regex', $param);
            }
        }
    }

    /**
     * Validates JSON string
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function jsonString(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            $value = htmlspecialchars_decode($value, ENT_QUOTES);

            if (!is_object(json_decode($value))) {
                $this->addError($field, 'jsonString', $param);
            }
        }
    }

    /**
     * Validates same value for both fields
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function same(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if ($value != $this->data[$param]) {
                $this->addError($field, 'same', $param);
            }
        }
    }

    /**
     * Validates uniqueness
     * @param string $field
     * @param mixed $value
     * @param string $param
     * @throws \Quantum\Exceptions\DiException
     */
    protected function unique(string $field, $value, string $param)
    {
        if (!empty($value)) {
            $model = Di::get(ModelFactory::class)->get(ucfirst($param));

            $row = $model->findOneBy($field, $value);

            if ($row->count()) {
                $this->addError($field, 'unique', null);
            }
        }
    }

    /**
     * Validates file size
     * @param string $field
     * @param object $value
     * @param mixed $param
     */
    protected function fileSize(string $field, object $value, $param)
    {
        if (!empty($value)) {
            $file = new File($value);

            if (!is_array($param)) {
                if ($file->getSize() > $param) {
                    $this->addError($field, 'fileSize', $param);
                }
            } else {
                if ($file->getSize() < $param[0] || $file->getSize() > $param[1]) {
                    $this->addError($field, 'fileSize', $param);
                }
            }
        }
    }

    /**
     * Validates file mime type
     * @param string $field
     * @param object $value
     * @param mixed $param
     */
    protected function fileMimeType(string $field, object $value, $param)
    {
        if (!empty($value)) {
            $file = new File($value);

            if (!is_array($param)) {
                if ($file->getMimetype() != $param) {
                    $this->addError($field, 'fileMimeType', $param);
                }
            } else {
                if (!in_array($file->getMimetype(), $param)) {
                    $this->addError($field, 'fileMimeType', $param);
                }
            }
        }
    }

    /**
     * Validates file extension
     * @param string $field
     * @param object $value
     * @param mixed $param
     */
    protected function fileExtension(string $field, object $value, $param)
    {
        if (!empty($value)) {
            $file = new File($value);

            if (!is_array($param)) {
                if ($file->getExtension() != $param) {
                    $this->addError($field, 'fileExtension', $param);
                }
            } else {
                if (!in_array($file->getExtension(), $param)) {
                    $this->addError($field, 'fileExtension', $param);
                }
            }
        }
    }

    /**
     * Validates image dimensions
     * @param string $field
     * @param object $value
     * @param array $param
     */
    protected function imageDimensions(string $field, object $value, array $param)
    {
        if (!empty($value)) {
            $file = new File($value);

            $dimensions = $file->getDimensions();

            if (!empty($dimensions)) {
                if ($dimensions['width'] != $param[0] || $dimensions['height'] != $param[1]) {
                    $this->addError($field, 'imageDimensions', $param);
                }
            }
        }
    }
}