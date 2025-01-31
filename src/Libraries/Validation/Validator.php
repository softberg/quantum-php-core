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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Validation;

use Quantum\Libraries\Validation\Rules\Resource;
use Quantum\Libraries\Validation\Rules\General;
use Quantum\Libraries\Validation\Rules\Length;
use Quantum\Libraries\Validation\Rules\Lists;
use Quantum\Libraries\Validation\Rules\Type;
use Quantum\Libraries\Validation\Rules\File;
use Closure;

/**
 * Class Validator
 * @package Quantum\Libraries\Validator
 */
class Validator
{

    use General;
    use Type;
    use File;
    use Lists;
    use Length;
    use Resource;

    /**
     * Rules
     * @var array
     */
    private $rules = [];

    /**
     * Validation Errors
     * @var array
     */
    private $errors = [];

    /**
     * Request data
     * @var array
     */
    private $data = [];

    /**
     * Custom validations
     * @var array
     */
    private $customValidations = [];

    /**
     * Add a rules for given field
     * @param string $field
     * @param array $rules
     */
    public function addRule(string $field, array $rules)
    {
        if (!empty($field)) {
            foreach ($rules as $rule) {
                if (!isset($this->rules[$field])) {
                    $this->rules[$field] = [];
                }

                $this->rules[$field][array_keys($rule)[0]] = array_values($rule)[0];
            }
        }
    }

    /**
     * Adds rules for multiple fields
     * @param array $rules
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $field => $params) {
            $this->addRule($field, $params);
        }
    }

    /**
     * Updates the single rule in rules list for given field
     * @param string $field
     * @param array $rule
     */
    public function updateRule(string $field, array $rule)
    {
        if (!empty($field)) {
            if (isset($this->rules[$field][array_keys($rule)[0]])) {
                $this->rules[$field][array_keys($rule)[0]] = array_values($rule)[0];
            }
        }
    }

    /**
     * Deletes the the rule in rules list for given field
     * @param string $field
     * @param string|null $rule
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
     * Flush ruels
     */
    public function flushRules()
    {
        $this->rules = [];
        $this->flushErrors();
    }

    /**
     * Validates the data against the rules
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $this->data = $data;

        if (count($this->rules)) {

            foreach ($this->rules as $field => $rule) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = '';
                }
            }

            foreach ($data as $field => $value) {

                if (isset($this->rules[$field])) {
                    foreach ($this->rules[$field] as $method => $param) {

                        if (is_callable([$this, $method])) {
                            $this->$method($field, $value, $param);
                        } elseif (isset($this->customValidations[$method])) {
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

        return !count($this->errors);
    }

    /**
     * Adds custom validation
     * @param string $rule
     * @param Closure $function
     */
    public function addValidation(string $rule, Closure $function)
    {
        if (!empty($rule) && is_callable($function)) {
            $this->customValidations[$rule] = $function;
        }
    }

    /**
     * Gets validation errors
     * @return array
     */
    public function getErrors(): array
    {
        if (count($this->errors)) {
            $messages = [];
            foreach ($this->errors as $field => $errors) {
                if (count($errors)) {
                    foreach ($errors as $rule => $param) {
                        $translationParams = [t('common.'.$field)];

                        if ($param) {
                            $translationParams[] = $param;
                        }

                        if (!isset($messages[$field])) {
                            $messages[$field] = [];
                        }

                        $messages[$field][] = t("validation.$rule", $translationParams);
                    }
                }
            }

            return $messages;
        }

        return [];
    }

    /**
     * Adds validation Error
     * @param string $field
     * @param string $rule
     * @param null|mixed $param
     */
    protected function addError(string $field, string $rule, $param = null)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][$rule] = $param;
    }

    /**
     * Flush errors
     */
    public function flushErrors()
    {
        $this->errors = [];
    }

    /**
     * Calls custom function defined by developer
     * @param Closure $function
     * @param array $data
     */
    protected function callCustomFunction(Closure $function, array $data)
    {
        if (!empty($data['value'])) {
            if (is_callable($function)) {
                if (!$function($data['value'], $data['param'])) {
                    $this->addError($data['field'], $data['rule'], $data['param']);
                }
            }
        }
    }
}