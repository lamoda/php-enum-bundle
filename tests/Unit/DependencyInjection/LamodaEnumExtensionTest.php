<?php

namespace Lamoda\EnumBundle\Tests\Unit\DependencyInjection;

use Lamoda\EnumBundle\DBAL\EnumTypeInitializer;
use Lamoda\EnumBundle\DependencyInjection\LamodaEnumExtension;
use Lamoda\EnumBundle\Naming\IdenticalNamingStrategy;
use Lamoda\EnumBundle\Naming\LowercaseNamingStrategy;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\DependencyInjection\LamodaEnumExtension
 * @covers \Lamoda\EnumBundle\DependencyInjection\Configuration
 */
final class LamodaEnumExtensionTest extends TestCase
{
    public function testExtensionLoadsEmptyConfig(): void
    {
        $builder = new ContainerBuilder();
        $extension = new LamodaEnumExtension();

        $extension->load([], $builder);

        self::assertTrue($builder->has(IdenticalNamingStrategy::class));
        self::assertTrue($builder->has(LowercaseNamingStrategy::class));
        self::assertTrue($builder->has(EnumTypeInitializer::class));
    }

    public function testExtensionLoadsFilledConfigs(): void
    {
        $builder = new ContainerBuilder();
        $extension = new LamodaEnumExtension();

        $extension->load(
            [
                [
                    'dbal_types' => [
                        'type_1' => TestEnum::class,
                        'type_2' => [
                            'class' => TestEnum::class,
                            'strategy' => 'lowercase',
                        ],
                    ],
                ],
            ],
            $builder
        );

        $initDef = $builder->getDefinition(EnumTypeInitializer::class);
        $calls = $initDef->getMethodCalls();
        self::assertCount(2, $calls);
        self::assertSame('initialize', $calls[0][0]);
        self::assertSame('type_1', $calls[0][1][0]);
        self::assertSame(TestEnum::class, $calls[0][1][1]);
        /** @var Reference $strat1 */
        $strat1 = $calls[0][1][2];
        self::assertInstanceOf(Reference::class, $strat1);
        self::assertSame(IdenticalNamingStrategy::class, (string) $strat1);

        self::assertSame('initialize', $calls[1][0]);
        self::assertSame('type_2', $calls[1][1][0]);
        self::assertSame(TestEnum::class, $calls[1][1][1]);
        /** @var Reference $strat1 */
        $strat1 = $calls[1][1][2];
        self::assertInstanceOf(Reference::class, $strat1);
        self::assertSame(LowercaseNamingStrategy::class, (string) $strat1);
    }
}
