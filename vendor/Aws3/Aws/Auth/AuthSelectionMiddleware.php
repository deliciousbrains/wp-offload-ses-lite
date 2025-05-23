<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\Auth;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\Api\Service;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\CommandInterface;
use Closure;
use DeliciousBrains\WP_Offload_SES\Aws3\GuzzleHttp\Promise\Promise;
/**
 * Handles auth scheme resolution. If a service models and auth scheme using
 * the `auth` trait and the operation or metadata levels, this middleware will
 * attempt to select the first compatible auth scheme it encounters and apply its
 * signature version to the command's `@context` property bag.
 *
 * IMPORTANT: this middleware must be added to the "build" step.
 *
 * @internal
 */
class AuthSelectionMiddleware
{
    /** @var callable */
    private $nextHandler;
    /** @var AuthSchemeResolverInterface */
    private $authResolver;
    /** @var Service */
    private $api;
    /**
     * Create a middleware wrapper function
     *
     * @param AuthSchemeResolverInterface $authResolver
     * @param Service $api
     * @return Closure
     */
    public static function wrap(AuthSchemeResolverInterface $authResolver, Service $api) : Closure
    {
        return function (callable $handler) use($authResolver, $api) {
            return new self($handler, $authResolver, $api);
        };
    }
    /**
     * @param callable $nextHandler
     * @param $authResolver
     * @param callable $identityProvider
     * @param Service $api
     */
    public function __construct(callable $nextHandler, AuthSchemeResolverInterface $authResolver, Service $api)
    {
        $this->nextHandler = $nextHandler;
        $this->authResolver = $authResolver;
        $this->api = $api;
    }
    /**
     * @param CommandInterface $command
     *
     * @return Promise
     */
    public function __invoke(CommandInterface $command)
    {
        $nextHandler = $this->nextHandler;
        $serviceAuth = $this->api->getMetadata('auth') ?: [];
        $operation = $this->api->getOperation($command->getName());
        $operationAuth = $operation['auth'] ?? [];
        $unsignedPayload = $operation['unsignedpayload'] ?? \false;
        $resolvableAuth = $operationAuth ?: $serviceAuth;
        if (!empty($resolvableAuth)) {
            if (isset($command['@context']['auth_scheme_resolver']) && $command['@context']['auth_scheme_resolver'] instanceof AuthSchemeResolverInterface) {
                $resolver = $command['@context']['auth_scheme_resolver'];
            } else {
                $resolver = $this->authResolver;
            }
            $selectedAuthScheme = $resolver->selectAuthScheme($resolvableAuth, ['unsigned_payload' => $unsignedPayload]);
            if (!empty($selectedAuthScheme)) {
                $command['@context']['signature_version'] = $selectedAuthScheme;
            }
        }
        return $nextHandler($command);
    }
}
