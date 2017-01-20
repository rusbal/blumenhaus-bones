<?php

namespace Rsu\Validator\Filters;


use Underscore\Types\Strings;

abstract class CancelValidatorFilter
{
    /**
     * Array of rules that is use for validation.
     *  [
     *      'email' => 'required|ifnotset:sameAsBilling',
     *      'color' => 'required|ifeq:card=Yes'
     *      'name' => 'required',
     *  ]
     * @var array
     */
    protected $rules;

    /**
     * Array of values, most probably $_POST.
     * @var array
     */
    protected $post;

    /**
     * CancelValidatorFilter constructor.
     * @param $rules
     * @param $post
     */
    public function __construct($rules, $post)
    {
        $this->rules = $rules;
        $this->post = $post;
    }

    /**
     * Run this filter.
     *
     * @return bool
     */
    public function check() {
        if ($rule = $this->doesRuleApply()) {
            /**
             * Rule is applied.
             */
            if ($this->isTrue($rule)) {
                /**
                 * Condition is met.  Do not cancel validation.
                 */
                return false;
            } else {
                /**
                 * Condition is not met.  Cancel validation.
                 */
                return true;
            }
        }

        /**
         * Rule is not applied.  Disregard this filter and move on.
         */
        return false;
    }

    /**
     * Checks if condition matches rule.
     *
     * @param  string      $condition
     * @return string|null $rule
     */
    final public function checkDoesRuleApply($condition)
    {
        foreach ($this->rules as $rule) {
            if (Strings::startsWith($rule, $condition)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Checks if rule is applied.
     *
     * @return boolean
     */
    abstract function doesRuleApply();

    /**
     * Checks if rule evaluates to true.
     *
     * @param $rule
     * @return boolean
     */
    abstract function isTrue($rule);

    /**
     * Checks that value is not null and not blank.
     *
     * @param $value
     * @return bool
     */
    protected function isSupplied($value)
    {
        return ! is_null($value) && $value != '';
    }

    /**
     * Split rule ensuring second part.
     *
     * @param $rule
     * @return array
     */
    protected function splitRule($rule)
    {
        $arr = explode(':', $rule);

        if (count($arr) == 1) {
            return [$rule, null];
        }
        return $arr;
    }
}