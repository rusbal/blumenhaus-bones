<?php

namespace Rsu\Validator\Filters;


use Underscore\Types\Strings;

class CancelValidatorFilterIfEq extends CancelValidatorFilter {

    /**
     * Gets rule or null when not used.
     *
     * @return null|string
     */
    function doesRuleApply()
    {
        return parent::checkDoesRuleApply('ifeq:');
    }

    /**
     * Evaluates this filter.
     *
     * @param $rule
     * @return bool
     */
    function isTrue($rule)
    {
        list(, $equation) = $this->splitRule($rule);
        list($field, $value) = explode('=', $equation, 2);
        return $this->post[$field] == $value;
    }
}