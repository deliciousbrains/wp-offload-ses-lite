<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\Auth;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Identity\IdentityInterface;
/**
 * An AuthSchemeResolver object determines which auth scheme will be used for request signing.
 */
interface AuthSchemeResolverInterface
{
    /**
     * Selects an auth scheme for request signing.
     *
     * @param array $authSchemes a priority-ordered list of authentication schemes.
     * @param IdentityInterface $identity Credentials to be used in request signing.
     *
     * @return string
     */
    public function selectAuthScheme(array $authSchemes, array $args) : ?string;
}
