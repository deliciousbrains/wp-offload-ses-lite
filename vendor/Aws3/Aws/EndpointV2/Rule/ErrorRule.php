<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\EndpointV2\Rule;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\EndpointV2\Ruleset\RulesetStandardLibrary;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Exception\UnresolvedEndpointException;
class ErrorRule extends AbstractRule
{
    /** @var array */
    private $error;
    public function __construct($definition)
    {
        parent::__construct($definition);
        $this->error = $definition['error'];
    }
    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * If an error rule's conditions are met, raise an
     * UnresolvedEndpointError containing the fully resolved error string.
     *
     * @return null
     * @throws UnresolvedEndpointException
     */
    public function evaluate(array $inputParameters, RulesetStandardLibrary $standardLibrary)
    {
        if ($this->evaluateConditions($inputParameters, $standardLibrary)) {
            $message = $standardLibrary->resolveValue($this->error, $inputParameters);
            throw new UnresolvedEndpointException($message);
        }
        return \false;
    }
}
