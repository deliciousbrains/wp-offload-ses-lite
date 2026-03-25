<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Test;

use DeliciousBrains\WP_Offload_SES\PHPUnit\Framework\MockObject\MockObject;
use DeliciousBrains\WP_Offload_SES\PHPUnit\Framework\TestCase;
use DeliciousBrains\WP_Offload_SES\Psr\Log\LoggerInterface;
use DeliciousBrains\WP_Offload_SES\Psr\Log\NullLogger;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\HttpClient\MockHttpClient;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Dumper\XliffFileDumper;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Loader\ArrayLoader;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Loader\LoaderInterface;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\Provider\ProviderInterface;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\TranslatorBag;
use DeliciousBrains\WP_Offload_SES\Symfony\Component\Translation\TranslatorBagInterface;
use DeliciousBrains\WP_Offload_SES\Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 * A test case to ease testing a translation provider.
 *
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
abstract class ProviderTestCase extends TestCase
{
    protected HttpClientInterface $client;
    protected LoggerInterface|MockObject $logger;
    protected string $defaultLocale;
    protected LoaderInterface|MockObject $loader;
    protected XliffFileDumper|MockObject $xliffFileDumper;
    protected TranslatorBagInterface|MockObject $translatorBag;
    public static abstract function createProvider(HttpClientInterface $client, LoaderInterface $loader, LoggerInterface $logger, string $defaultLocale, string $endpoint) : ProviderInterface;
    /**
     * @return iterable<array{0: ProviderInterface, 1: string}>
     */
    public static abstract function toStringProvider() : iterable;
    /**
     * @dataProvider toStringProvider
     */
    public function testToString(ProviderInterface $provider, string $expected)
    {
        $this->assertSame($expected, (string) $provider);
    }
    protected function getClient() : MockHttpClient
    {
        return $this->client ??= new MockHttpClient();
    }
    protected function getLoader() : LoaderInterface
    {
        return $this->loader ??= new ArrayLoader();
    }
    protected function getLogger() : LoggerInterface
    {
        return $this->logger ??= new NullLogger();
    }
    protected function getDefaultLocale() : string
    {
        return $this->defaultLocale ??= 'en';
    }
    protected function getXliffFileDumper() : XliffFileDumper
    {
        return $this->xliffFileDumper ??= new XliffFileDumper();
    }
    protected function getTranslatorBag() : TranslatorBagInterface
    {
        return $this->translatorBag ??= new TranslatorBag();
    }
}
