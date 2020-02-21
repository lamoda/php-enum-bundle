<?php

namespace Lamoda\EnumBundle\Tests\Unit\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;
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

    protected function setUp(): void
    {
        $typeReflectionClass = new \ReflectionClass(Type::class);

        if ($typeReflectionClass->hasProperty('_typesMap')) {
            $typesMapProperty = $typeReflectionClass->getProperty('_typesMap');
            $typesMapProperty->setAccessible(true);
            $typesMapProperty->setValue([]);
        } else {
            $registry = new TypeRegistry();
            $typesMapProperty = $typeReflectionClass->getProperty('typeRegistry');
            $typesMapProperty->setAccessible(true);
            $typesMapProperty->setValue($registry);
        }
    }

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
     * @param bool   $enumNameTypeMapping
     * @param string $expectedType
     * @param array  $expectedMappedTypes
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @dataProvider dataInitializerLoadsTypeWithMappingFlag
     */
    public function testInitializerLoadsTypeWithMappingFlag(
        bool $enumNameTypeMapping,
        string $expectedType,
        array $expectedMappedTypes
    ): void {
        $initalizer = new EnumTypeInitializer();
        $initalizer->initialize(
            self::TESTED_TYPE,
            TestEnum::class,
            new IdenticalNamingStrategy(),
            $enumNameTypeMapping
        );

        $platform = $this->createMock(AbstractPlatform::class);
        $platform->method('getVarcharTypeDeclarationSQL')
            ->willReturn('VARCHAR(255)');

        $type = Type::getType(self::TESTED_TYPE);

        self::assertEquals($enumNameTypeMapping, $type->isEnumNameTypeMapping());
        self::assertEquals($expectedType, $type->getSQLDeclaration([], $platform));
        self::assertEquals($expectedMappedTypes, $type->getMappedDatabaseTypes($platform));
    }

    public function dataInitializerLoadsTypeWithMappingFlag()
    {
        return [
            [false, 'VARCHAR(255)', [self::TESTED_TYPE => 'string']],
            [true, self::TESTED_TYPE, [self::TESTED_TYPE => self::TESTED_TYPE]]
        ];
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
