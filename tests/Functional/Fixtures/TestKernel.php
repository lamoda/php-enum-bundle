<?php

namespace Lamoda\EnumBundle\Tests\Functional\Fixtures;

use Lamoda\EnumBundle\LamodaEnumBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    /** @var string */
    private $configFileName = 'config.yml';

    /** @var string */
    private $cacheDirPostfix = '';

    /** {@inheritdoc} */
    public function registerBundles()
    {
        return [
            new LamodaEnumBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/'.$this->getConfigFileName());
    }

    public function getCacheDir()
    {
        return __DIR__.'/../../../build/cache'.$this->getCacheDirPostfix();
    }

    public function getLogDir()
    {
        return __DIR__.'/../../../build/logs';
    }

    /**
     * @return string
     */
    public function getConfigFileName(): string
    {
        return $this->configFileName;
    }

    /**
     * @param string $configFileName
     */
    public function setConfigFileName(string $configFileName): void
    {
        $this->configFileName = $configFileName;
    }

    public function getCacheDirPostfix(): string
    {
        return $this->cacheDirPostfix;
    }

    public function setCacheDirPostfix(string $cacheDirPostfix): void
    {
        $this->cacheDirPostfix = $cacheDirPostfix;
    }
}
