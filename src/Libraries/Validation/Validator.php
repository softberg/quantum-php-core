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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Validation;

/**
 * Validator class
 *
 * @package Quantum
 * @subpackage Libraries.Validator
 * @category Libraries
 */

class Validator
{

    /**
     * Rules
     *
     * @var array
     */
    private $rules = [];

    /**
     * Validation Errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * Custom validations
     *
     * @var array
     */
    private $customValidations = [];

    /**
     * Add rule
     *
     * Add rule in rules list
     *
     * @param string $field
     * @param array $rules
     *
     * @return void
     */
    public function addRule(string $field, array $rules)
    {
        if (!empty($field) && is_array($rules)) {
            foreach ($rules as $rule) {
                if (!isset($this->rules[$field])) {
                    $this->rules[$field] = [];
                }

                $this->rules[$field][array_keys($rule)[0]] = array_values($rule)[0];
            }
        }
    }

    /**
     * Add rules
     *
     * Add rules in rules list
     *
     * @param array $rules
     *
     * @return void
     */
    public function addRules(array $rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $field => $params) {
                $this->addRule($field, $params);
            }
        }
    }

    /**
     * Update rules
     *
     * Update rule in rules list
     *
     * @param string $field
     * @param array $rule
     *
     * @return void
     */
    public function updateRule(string $field, array $rule)
    {
        if (!empty($field) && is_array($rule)) {
            if (isset($this->rules[$field]) && isset($this->rules[$field][array_keys($rule)[0]])) {
                $this->rules[$field][array_keys($rule)[0]] = array_values($rule)[0];
            }
        }
    }

    /**
     * Delete rules
     *
     * Delete rule in rules list
     *
     * @param string $field
     * @param string|null $rule
     *
     * @return void
     */
    public function deleteRule(string $field, string $rule = null)
    {
        if (!empty($field)) {
            if (isset($this->rules[$field])) {
                if (!empty($rule) && isset($this->rules[$field][$rule])) {
                    unset($this->rules[$field][$rule]);
                } else {
                    if (empty($rule)) {
                        unset($this->rules[$field]);
                    }
                }
            }
        }
    }

    /**
     * Validates by rules
     *
     * @param array $data
     *
     * @return mixed
     */
    public function isValid(array $data)
    {
        if (count($this->rules) && count($data)) {
            foreach ($data as $field => $value) {
                if (isset($this->rules[$field])) {
                    foreach ($this->rules[$field] as $method => $param) {
                        if (is_callable([$this, $method])) {
                            $this->$method($field, $value, $param);
                        } elseif(isset($this->customValidations[$method])) {
                            $data = [
                                'rule' => $method, 
                                'field' => $field, 
                                'value' => $value, 
                                'param' => $param ?? null
                            ];

                            $this->callCustomFunction($this->customValidations[$method], $data);
                        }
                    }
                }
            }
        }

        return count($this->errors) ? false : true;
    }

    /**
     * Add custom validation
     *
     * @param string $rule
     * @param Closure $function
     * @param null|mixed $params
     *
     * @return mixed
     */
    public function addValidation(string $rule, \Closure $function)
    {
        if(!empty($rule) && is_callable($function)) {
            $this->customValidations[$rule] =  $function;
        }
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        if (count($this->errors)) {
            $messages = [];
            foreach ($this->errors as $field => $errors) {
                if (count($errors)) {
                    foreach ($errors as $rule => $param) {
                        $translatoinParams = [ucfirst($field)];

                        if ($param) {
                            $translatoinParams[] = $param;
                        }

                        if (!isset($messages[$field])) {
                            $messages[$field] = [];
                        }

                        $messages[$field][] = t("validation.$rule", $translatoinParams);
                    }
                }
            }

            return $messages;
        }

        return [];
    }

     /**
     * Call custome function defined by developer
     *
     * @param Closure $function : boolean
     * @param array $data
     *
     * @return void
     */
    private function callCustomFunction(\Closure $function, array $data) {
        if (empty($data['value'])) {
            return true;
        }

        if(is_callable($function)) {
            if(!$function($data['value'], $data['param'])) {
                $this->addError($data['field'], $data['rule'], $data['param']);
            }
        }
    }

    /**
     * Check Field Required
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function required(string $field, string $value, $param = null)
    {
        if ($value === false || $value === 0 || $value === 0.0 || $value === '0' || empty($value)) {
            $this->addError($field, 'required', $param);
        }
    }

    /**
     * Check Email
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function email(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'email', $param);
        }
    }

    /**
     * Check Min Length
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function minLen(string $field, string $value, $param = null)
    {
        $error = false;

        if (empty($value)) {
            return true;
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($value) < (int) $param) {
                $error = true;
            }
        } else {
            if (strlen($value) < (int) $param) {
                $error = true;
            }
        }

        if ($error) {
            $this->addError($field, 'minLen', $param);
        }
    }

    /**
     * Check Max Length
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function maxLen(string $field, string $value, $param = null)
    {
        $error = false;

        if (empty($value)) {
            return true;
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($value) > (int) $param) {
                $error = true;
            }
        } else {
            if (strlen($value) > (int) $param) {
                $error = true;
            }
        }

        if ($error) {
            $this->addError($field, 'maxLen', $param);
        }
    }

    /**
     * Check Exact length
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function exactLen(string $field, string $value, $param = null)
    {
        $error = false;

        if (empty($value)) {
            return true;
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($value) !== (int) $param) {
                $error = true;
            }
        } else {
            if (strlen($value) !== (int) $param) {
                $error = true;
            }
        }

        if ($error) {
            $this->addError($field, 'exactLen', $param);
        }
    }

    /**
     * Check Alpha Characters
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function alpha(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) == false) {
            $this->addError($field, 'alpha', $param);
        }
    }

    /**
     * Check Alpha Numeric Characters
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function alphaNumeric(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) == false) {
            $this->addError($field, 'alphaNumeric', $param);
        }
    }

    /**
     * Check Alpha Dash Characters
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function alphaDash(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $value) == false) {
            $this->addError($field, 'alphaDash', $param);
        }
    }

    /**
     * Check Alpha Numeric Space Characters
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function alphaSpace(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $value) == false) {
            $this->addError($field, 'alphaSpace', $param);
        }
    }

    /**
     * Check Numeric Value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function numeric(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!is_numeric($value)) {
            $this->addError($field, 'numeric', $param);
        }
    }

    /**
     * Check Integer Value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function integer(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, 'integer', $param);
        }
    }

    /**
     * Check Boolean Value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function boolean(string $field, string $value, $param = null)
    {
        if (empty($value) && $value !== 0) {
            return true;
        }

        $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

        if (!in_array($value, $booleans, true)) {
            $this->addError($field, 'boolean', $param);
        }
    }

    /**
     * Check Float Value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function float(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $this->addError($field, 'float', $param);
        }
    }

    /**
     * Check for valid URL or subdomain
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function url(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            $this->addError($field, 'url', $param);
        }
    }

    /**
     * Check to see if the url exists
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function urlExists(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $error = false;

        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            $url = parse_url(strtolower($value));

            if (isset($url['host'])) {
                $url = $url['host'];
            }

            if (function_exists('checkdnsrr') && function_exists('idn_to_ascii')) {
                if (checkdnsrr(idn_to_ascii($url), 'A') === false) {
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

    /**
     * Check for valid IP address
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function ip(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            $this->addError($field, 'ip', $param);
        }
    }

    /**
     * Check for valid IPv4 address
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function ipv4(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            $this->addError($field, 'ipv4', $param);
        }
    }

    /**
     * Check for valid IPv6 address
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function ipv6(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            $this->addError($field, 'ipv6', $param);
        }
    }

    /**
     * Check for a valid credit card number
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function creditCard(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $number = preg_replace('/\D/', '', $value);

        if (function_exists('mb_strlen')) {
            $number_length = mb_strlen($number);
        } else {
            $number_length = strlen($number);
        }

        if ($number_length == 0) {
            return $this->addError($field, 'creditCard', $param);
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

    /**
     * Check for a valid format human name
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function name(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (preg_match("/^([a-z \p{L} '-])+$/i", $value) == false) {
            $this->addError($field, 'name', $param);
        }
    }

    /**
     * Verify that a value is contained within the pre-defined value set
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function contains(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

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

    /**
     * Verify that a value is contained within the pre-defined value set. // "value1;value2;value3;..."
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function containsList(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $param = trim(strtolower($param));
        $value = trim(strtolower($value));
        $param = explode(';', $param);

        if (!in_array($value, $param)) {
            $this->addError($field, 'containsList', 'null');
        }
    }

    /**
     * Verify that a value is not contained within the pre-defined value set. // "value1;value2;value3;..."
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function doesntContainsList(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $param = trim(strtolower($param));
        $value = trim(strtolower($value));
        $param = explode(';', $param);

        if (in_array($value, $param)) {
            $this->addError($field, 'doesntContainsList', null);
        }
    }

    /**
     * Checks that the provided string is a likely street address.
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function streetAddress(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $hasLetter = preg_match('/[a-zA-Z]/', $value);
        $hasDigit = preg_match('/\d/', $value);
        $hasSpace = preg_match('/\s/', $value);

        $passes = $hasLetter && $hasDigit && $hasSpace;

        if (!$passes) {
            $this->addError($field, 'streetAddress', $param);
        }
    }

    /**
     * Check for a valid IBAN
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function iban(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

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

    /**
     * Determine if the provided numeric value is lower to a specific value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function minNumeric(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!is_numeric($value) || !is_numeric($param) || ($value < $param)) {
            $this->addError($field, 'minNumeric', $param);
        }
    }

    /**
     * Determine if the provided numeric value is higher to a specific value
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function maxNumeric(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!is_numeric($value) || !is_numeric($param) || ($value > $param)) {
            $this->addError($field, 'maxNumeric', $param);
        }
    }

    /**
     * Determine if the provided input is a valid date
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function date(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!$param) {
            $cdate1 = date('Y-m-d', strtotime($value));
            $cdate2 = date('Y-m-d H:i:s', strtotime($value));

            if ($cdate1 != $value && $cdate2 != $value) {
                $this->addError($field, 'date', $param);
            }
        } else {
            $param = 'Y-m-d';
            $date = \DateTime::createFromFormat($param, $value);

            if ($date === false || $value != date($param, $date->getTimestamp())) {
                $this->addError($field, 'date', $param);
            }
        }
    }

    /**
     * Ensures the value starts with a certain character / set of character
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function starts(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (strpos($value, $param) !== 0) {
            $this->addError($field, 'starts', $param);
        }
    }

    /**
     * Validate phone number // 555-555-5555 , 5555425555, 555 555 5555, 1(519) 555-4444, 1 (519) 555-4422, 1-555-555-5555
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function phoneNumber(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $regex = '/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i';

        if (!preg_match($regex, $value)) {
            $this->addError($field, 'phoneNumber', $param);
        }
    }

    /**
     * Custom regex validator
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function regex(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        if (!preg_match($param, $value)) {
            $this->addError($field, 'regex', $param);
        }
    }

    /**
     * JSON validator
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function jsonString(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $value = htmlspecialchars_decode($value, ENT_QUOTES);

        if (!is_string($value) || !is_object(json_decode($value))) {
            $this->addError($field, 'jsonString', $param);
        }
    }

    /**
     * Determine if the provided input meets age requirement
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function minAge(string $field, string $value, $param = null)
    {
        if (empty($value)) {
            return true;
        }

        $cdate1 = new \DateTime(date('Y-m-d', strtotime($value)));
        $today = new \DateTime(date('d-m-Y'));

        $interval = $cdate1->diff($today);
        $age = $interval->y;

        if ($age <= $param) {
            $this->addError($field, 'minAge', $param);
        }
    }

    /**
     * Validate uniqueness
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     * @return void
     */
    private function unique(string $field, string $value, $param = null)
    {
        $qtInstance = qt_instance();
        $model = $qtInstance->modelFactory(ucfirst($param));

        $model->findOneBy($field, $value);

        if (!empty($model->asArray())) {
            $this->addError($field, 'unique', null);
        }
    }

    /**
     * Add Validation Error
     *
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     *
     * @return void
     */
    private function addError($field, $rule, $param = null)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][$rule] = $param;
    }

}
