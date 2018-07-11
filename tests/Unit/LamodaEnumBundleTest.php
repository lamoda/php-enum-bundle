<?php

namespace Lamoda\EnumBundle\Tests\Unit;

use Lamoda\EnumBundle\DBAL\EnumTypeInitializer;
use Lamoda\EnumBundle\LamodaEnumBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\LamodaEnumBundle
 */
final class LamodaEnumBundleTest extends TestCase
{
    public function testBundleInitsInitializerOnBoot(): void
    {
        $bundle = new LamodaEnumBundle();
        $container = $this->createMock(ContainerInterface::class);

        $bundle->setContainer($container);

        $container->expects($this->once())
            ->method('get')
            ->with(EnumTypeInitializer::class);

        $bundle->boot();
    }
}
