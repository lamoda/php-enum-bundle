<?php

namespace Lamoda\EnumBundle\Tests\Functional\Fixtures;

use Lamoda\EnumBundle\LamodaEnumBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    /** {@inheritdoc} */
    public function registerBundles()
    {
        return [
            new LamodaEnumBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getCacheDir()
    {
        return __DIR__.'/../../../build/cache';
    }

    public function getLogDir()
    {
        return __DIR__.'/../../../build/logs';
    }
}
