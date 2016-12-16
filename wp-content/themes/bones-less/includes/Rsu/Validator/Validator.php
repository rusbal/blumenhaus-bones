<?php

namespace Rsu\Validator;


use Rsu\Validator\Filters\CancelValidatorFilterIfEq;
use Rsu\Validator\Filters\CancelValidatorFilterIfNotSet;
use Rsu\Validator\Filters\CancelValidatorFilterIfSet;

class Validator {

    const LANG_IS_REQUIRED = 'ist ein pflichtfeld';

    /**
     * Array of rules that is use for validation.
     *  [
     *      'email' => 'required|ifnotset:sameAsBilling',
     *      'color' => 'required|ifeq:card=Yes'
     *      'name' => 'required',
     *  ]
     * @var [string]
     */
    protected $rules;

    /**
     * Array of values ($_POST)
     *
     * @var array
     */
    protected $post;

    /**
     * Array of errors stored for each field.
     *
     * @var [string]
     */
    protected $errors = [];

    /**
     * Array of CancelValidationFilters.
     * These filters cancel the validation.
     *
     * @var [class]
     */
    protected $cancelValidators = [
        CancelValidatorFilterIfEq::class,
        CancelValidatorFilterIfSet::class,
        CancelValidatorFilterIfNotSet::class
    ];

    /**
     * Validator constructor.
     * @param null $rules
     * @param null $post
     */
    public function __construct($rules = null, $post = null) {
		$this->rules = $rules;
		$this->post = $post;
	}

    /**
     * Run all validation rules.
     *
     * @return bool
     */
    public function run()
	{
		$isValid = true;

		foreach ($this->rules as $name => $rule) {
			$isSatisfied = $this->evaluateField($name, $rule);

			if ($isValid) {
				$isValid = $isSatisfied;
			}
		}

		return $isValid;
	}

    /**
     * Alias to run().
     * @return bool
     */
    public function success()
    {
        return $this->run();
    }

    /**
     * Returns error message of field of null.
     *
     * @param $name
     * @return null|string
     */
    public function error($name)
	{
	    if (! isset($this->errors[$name])) {
            if (! isset($this->rules[$name])) {
                return null;
            }

            $rule = $this->rules[$name];

            if ($this->evaluateField($name, $rule)) {
                return null;
            }
        }

		return '<div class="error">' . str_replace('_', ' ', $this->errors[$name]) . '</div>';
	}

    /**
     * Evaluates the rules on a field.
     *
     * @param $name
     * @param $rule
     * @return bool
     */
    private function evaluateField($name, $rule)
	{
		$value = isset($this->post[$name]) ? $this->post[$name] : null;
		$rules = explode('|', $rule);

        foreach ($this->cancelValidators as $cancelValidator) {
            if ((new $cancelValidator($rules, $this->post))->check()) {
                return true; // Cancel validation.
            }
        }

		foreach ($rules as $rule) {
			if ($this->evalRule($name, $value, $rule)) {
				continue;
			} else {
				return false;
			}
		}

		return true;
	}

    /**
     * Evaluates rule.
     *
     * @param $name
     * @param $value
     * @param $rule
     * @return bool
     */
    private function evalRule($name, $value, $rule)
	{
		list($ruleName) = $this->splitRule($rule);

		if ($ruleName == 'required') {
			$satisfied = $this->isSupplied($value);

			if (! $satisfied) {
				$this->errors[$name] = ucfirst($name) . ' ' . self::LANG_IS_REQUIRED;
			}
			return $satisfied;
		}

		return true;
	}

    /**
     * Splits a rule ensuring the presence of second part.
     *
     * @param $rule
     * @return array
     */
    private function splitRule($rule)
    {
        $arr = explode(':', $rule);

        if (count($arr) == 1) {
            return [$rule, null];
        }
        return $arr;
    }

    /**
     * Checks if value is not null and not empty string.
     *
     * @param $value
     * @return bool
     */
    private function isSupplied($value)
	{
		return ! is_null($value) && $value != '';
	}
}