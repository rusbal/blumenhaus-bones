<?php

namespace Rsu\Validator;


use Underscore\Types\Strings;

class Validator {
	protected $rules;
	protected $post;
	protected $errors = [];

	public function __construct($rules = null, $post = null) {
		$this->rules = $rules;
		$this->post = $post;
	}

	public function success()
	{
		return $this->run();
	}

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

	public function error($name)
	{
		if (! $this->rules) {
			return null;
		}

		$this->run();

		if (! isset($this->errors[$name])) {
			return null;
		}

		return '<div class="error">' . str_replace('_', ' ', $this->errors[$name]) . '</div>';
	}

	private function evaluateField($name, $rule)
	{
		$value = isset($this->post[$name]) ? $this->post[$name] : null;
		$rules = explode('|', $rule);

		/**
		 * Check if not set
		 */
		$ifNotSetRule = $this->getIfNotSet($rules);

		if ($ifNotSetRule) {
			if ($this->ifsetContinue($ifNotSetRule)) {
				/**
				 * We only need to check if this is not set.  It's set so no more test.
				 */
				return true;
			}
		}

		/**
		 * Check if set
		 */
		$ifSetRule = $this->getIfSet($rules);

		if ($ifSetRule) {
			if (! $this->ifsetContinue($ifSetRule)) {
				/**
				 * Ifset is not satisfied, thus, no need to validate.
				 */
				return true;
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

	private function getIfSet($rules)
	{
		foreach ($rules as $rule) {
			if (Strings::startsWith($rule, 'ifset:')) {
				return $rule;
			}
		}

		return null;
	}

	private function getIfNotSet($rules)
	{
		foreach ($rules as $rule) {
			if (Strings::startsWith($rule, 'ifnotset:')) {
				return $rule;
			}
		}

		return null;
	}

	private function splitRule($rule)
	{
		$arr = explode(':', $rule);

		if (count($arr) == 1) {
			return [$rule, null];
		}
		return $arr;
	}

	private function evalRule($name, $value, $rule)
	{
		list($ruleName, $ruleParam) = $this->splitRule($rule);

		if ($ruleName == 'required') {
			$satisfied = $this->isSupplied($value);

			if (! $satisfied) {
				$this->errors[$name] = ucfirst($name) . ' is required.';
			}
			return $satisfied;
		}

		return true;
	}

	private function ifsetContinue($rule)
	{
		list($ruleName, $requiredKeyToContinueTesting) = $this->splitRule($rule);

		/**
		 * Check if required key (contained in $requiredValueToContinueTesting) is set.
		 */
		if (! isset($this->post[$requiredKeyToContinueTesting])) {
			return false;
		}

		return $this->isSupplied($this->post[$requiredKeyToContinueTesting]);
	}

	private function isSupplied($value)
	{
		return ! is_null($value) && $value != '';
	}
}