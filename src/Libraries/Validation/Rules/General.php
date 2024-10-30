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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Validation\Rules;

use Quantum\Libraries\Captcha\CaptchaManager;
use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\CaptchaException;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\ModelException;
use Quantum\Exceptions\DiException;
use Quantum\Factory\ModelFactory;
use ReflectionException;

/**
 * Trait General
 * @package Quantum\Libraries\Validation
 */
trait General
{

    /**
     * Adds validation Error
     * @param string $field
     * @param string $rule
     * @param mixed|null $param
     */
    abstract protected function addError(string $field, string $rule, $param = null);

    /**
     * Checks Field Required
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function required(string $field, string $value, $param = null)
    {
        if ($value == false || empty($value)) {
            $this->addError($field, 'required', $param);
        }
    }

    /**
     * Checks Email
     * @param string $field
     * @param string $value
     * @param null|mixed $param
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
     * Check Captcha
     * @param string $field
     * @param string $value
     * @param $param
     * @return void
     * @throws CaptchaException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    protected function captcha(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            $captcha = CaptchaManager::getHandler();

            if (!$captcha->verify($value)){
                $errorCode = $captcha->getErrorMessage();
                $this->addError($field, 'captcha.'.$errorCode, $param);
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
     *  Validates uniqueness
     * @param string $field
     * @param $value
     * @param string $param
     * @return void
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws DatabaseException
     * @throws ModelException
     */
    protected function unique(string $field, $value, string $param)
    {
        if (!empty($value)) {
            $model = ModelFactory::get(ucfirst($param));

            $row = $model->findOneBy($field, $value);

            if ($row->count()) {
                $this->addError($field, 'unique', null);
            }
        }
    }

}