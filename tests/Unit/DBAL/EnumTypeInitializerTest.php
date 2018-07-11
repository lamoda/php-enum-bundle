<?php

namespace Lamoda\EnumBundle\Tests\Unit\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\DBAL\EnumType;
use Lamoda\EnumBundle\DBAL\EnumTypeInitializer;
use Lamoda\EnumBundle\Naming\IdenticalNamingStrategy;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestEnum;
use Paillechat\Enum\Enum;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\DBAL\EnumTypeInitializer
 */
final class EnumTypeInitializerTest extends TestCase
{
    private const TESTED_TYPE = 'test_type';

    public function testInitializerLoadsType(): void
    {
        $initalizer = new EnumTypeInitializer();
        $initalizer->initialize(self::TESTED_TYPE, TestEnum::class, new IdenticalNamingStrategy());

        self::assertTrue(Type::hasType(self::TESTED_TYPE));
        $type = Type::getType(self::TESTED_TYPE);
        self::assertInstanceOf(EnumType::class, $type);

        $enum = $type->convertToPHPValue('ONE', $this->createMock(AbstractPlatform::class));
        self::assertInstanceOf(TestEnum::class, $enum);
        self::assertEquals('ONE', (string) $enum);
    }

    /**
     * @runInSeparateProcess
     */
    public function testInitializationDoesNotHappenTwice(): void
    {
        $initalizer = new EnumTypeInitializer();
        $initalizer->initialize(self::TESTED_TYPE, SecondEnum::class, new IdenticalNamingStrategy());
        $initalizer->initialize(self::TESTED_TYPE, TestEnum::class, new IdenticalNamingStrategy());

        $type = Type::getType(self::TESTED_TYPE);

        self::assertEquals(
            SecondEnum::BAR(),
            $type->convertToPHPValue('BAR', $this->createMock(AbstractPlatform::class))
        );
    }
}

/**
 * @method static static BAR
 */
final class SecondEnum extends Enum
{
    protected const BAR = 'bar';
}
