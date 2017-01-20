<?php

namespace Rsu\Validator\Filters;


use Underscore\Types\Strings;

class CancelValidatorFilterIfSet extends CancelValidatorFilter {

    /**
     * Evaluates this filter.
     *
     * @return null|string
     */
    function doesRuleApply()
    {
        return parent::checkDoesRuleApply('ifset:');
    }

    /**
     * Evaluates this filter.
     *
     * @param $rule
     * @return bool
     */
    function isTrue($rule)
    {
        list(, $requiredKeyToContinueTesting) = $this->splitRule($rule);

        if (! isset($this->post[$requiredKeyToContinueTesting])) {
            return false;
        }

        return $this->isSupplied($this->post[$requiredKeyToContinueTesting]);
    }
}