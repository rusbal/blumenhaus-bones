<?php

namespace Rsu\Validator\Filters;


use Underscore\Types\Strings;

class CancelValidatorFilterIfNotSet extends CancelValidatorFilter {

    /**
     * Evaluates this filter.
     *
     * @return null|string
     */
    public function doesRuleApply()
    {
        return parent::checkDoesRuleApply('ifnotset:');
    }

    /**
     * Evaluates this filter.
     *
     * @param $rule
     * @return bool
     */
    public function isTrue($rule)
    {
        list(, $requiredKeyToContinueTesting) = $this->splitRule($rule);

        if (! isset($this->post[$requiredKeyToContinueTesting])) {
            return true;
        }

        return (! $this->isSupplied($this->post[$requiredKeyToContinueTesting]));
    }
}