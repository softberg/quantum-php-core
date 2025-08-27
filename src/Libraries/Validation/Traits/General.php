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

use Quantum\Libraries\Captcha\Factories\CaptchaFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;
use DateTime;

/**
 * Trait General
 * @package Quantum\Libraries\Validation
 */
trait General
{

    /**
     * Checks Field Required
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function required(string $field, string $value): bool
    {
        return !empty($value);
    }

    /**
     * Checks Email
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function email(string $field, string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Checks for a valid credit card number
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function creditCard(string $field, string $value): bool
    {
        $number = preg_replace('/\D/', '', $value);

        if (function_exists('mb_strlen')) {
            $number_length = mb_strlen($number);
        } else {
            $number_length = strlen($number);
        }

        if ($number_length == 0) {
            $this->addError($field, 'creditCard');
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

        return $total % 10 == 0;
    }

    /**
     * Checks for a valid IBAN
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function iban(string $field, string $value): bool
    {
        static $character = [
            'A' => 10, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
            'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22,
            'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28,
            'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
            'Z' => 35, 'B' => 11,
        ];

        if (!preg_match("/\A[A-Z]{2}\d{2} ?[A-Z\d]{4}( ?\d{4}){1,} ?\d{1,4}\z/", $value)) {
            return false;
        }

        $iban = str_replace(' ', '', $value);
        $iban = substr($iban, 4) . substr($iban, 0, 4);
        $iban = strtr($iban, $character);

        return bcmod($iban, 97) == 1;
    }

    /**
     * Checks for a valid format human name
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function name(string $field, string $value): bool
    {
        return preg_match("/^([a-z \p{L} '-])+$/i", $value) === 1;
    }

    /**
     * Checks that the provided string is a likely street address.
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function streetAddress(string $field, string $value): bool
    {
        $hasLetter = preg_match('/[a-zA-Z]/', $value);
        $hasDigit = preg_match('/\d/', $value);
        $hasSpace = preg_match('/\s/', $value);

        return $hasLetter && $hasDigit && $hasSpace;
    }

    /**
     * Validates the phone number // 555-555-5555 , 5555425555, 555 555 5555, 1(519) 555-4444, 1 (519) 555-4422, +1-555-555-5555
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function phoneNumber(string $field, string $value): bool
    {
        $regex = '/^(\+*\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i';

        return preg_match($regex, $value) === 1;
    }

    /**
     * Determines if the provided input is a valid date
     * @param string $field
     * @param string $value
     * @param string|null $format
     * @return bool
     */
    protected function date(string $field, string $value, ?string $format = null): bool
    {
        if (!$format) {
            $cdate1 = date('Y-m-d', strtotime($value));
            $cdate2 = date('Y-m-d H:i:s', strtotime($value));

            return $cdate1 === $value || $cdate2 === $value;
        }

        $date = DateTime::createFromFormat($format, $value);

        return $date !== false && $value === $date->format($format);
    }

    /**
     * Ensures the value starts with a certain character / set of character
     * @param string $field
     * @param string $value
     * @param string|null $text
     * @return bool
     */
    protected function starts(string $field, string $value, ?string $text = null): bool
    {
        return strpos($value, $text) === 0;
    }

    /**
     * Custom regex validator
     * @param string $field
     * @param string $value
     * @param string $pattern
     * @return bool
     */
    protected function regex(string $field, string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Validates JSON string
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function jsonString(string $field, string $value): bool
    {
        $value = htmlspecialchars_decode($value, ENT_QUOTES);

        return is_object(json_decode($value));
    }

    /**
     * Validates same value for both fields
     * @param string $field
     * @param string $value
     * @param string $otherField
     * @return bool
     */
    protected function same(string $field, string $value, string $otherField): bool
    {
        return $value == $this->data[$otherField];
    }

    /**
     *  Validates uniqueness
     * @param string $field
     * @param $value
     * @param string $className
     * @return bool
     * @throws ModelException
     */
    protected function unique(string $field, $value, string $className): bool
    {
        $model = ModelFactory::get(ucfirst($className));

        $row = $model->findOneBy($field, $value);

        return !$row->count();
    }

    /**
     * Validates record existence
     * @param string $field
     * @param $value
     * @param string $className
     * @return bool
     * @throws ModelException
     */
    protected function exists(string $field, $value, string $className): bool
    {
        $model = ModelFactory::get(ucfirst($className));

        $row = $model->findOneBy($field, $value);

        return $row->count() > 0;
    }

    /**
     * Check Captcha
     * @param string $field
     * @param string $value
     * @return mixed|true
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    protected function captcha(string $field, string $value)
    {
        $captcha = CaptchaFactory::get();

        return $captcha->verify($value);
    }
}