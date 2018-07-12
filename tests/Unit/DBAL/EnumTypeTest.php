<?php

namespace Lamoda\EnumBundle\Tests\Unit\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\DBAL\EnumType;
use Lamoda\EnumBundle\Naming\LowercaseNamingStrategy;
use Lamoda\EnumBundle\Naming\NamingStrategyInterface;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestEnum;
use Paillechat\Enum\Enum;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\DBAL\EnumType
 */
final class EnumTypeTest extends TestCase
{
    private const TESTED_TYPE = 'abstract_enum_type';

    /**
     * @dataProvider getObjectToValueMap
     *
     * @param string $value
     * @param TestEnum $expectedStatus
     */
    public function testConvertToPHPValue(?string $value, ?TestEnum $expectedStatus): void
    {
        $actualValue = self::createType()->convertToPHPValue($value, $this->createPlatformMock());

        $this->assertEquals($expectedStatus, $actualValue);
    }

    /**
     * @dataProvider getObjectToValueMap
     *
     * @param string $expectedStatus
     * @param TestEnum $status
     */
    public function testConvertToDatabaseValue(?string $expectedStatus, ?TestEnum $status): void
    {
        $actualValue = self::createType()->convertToDatabaseValue($status, $this->createPlatformMock());

        $this->assertEquals($expectedStatus, $actualValue);
    }

    public function getObjectToValueMap(): array
    {
        return [
            ['ONE', TestEnum::ONE()],
            ['TWO', TestEnum::TWO()],
            [null, null],
        ];
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToPHPValueInvalidValue(): void
    {
        self::createType()->convertToPHPValue('UNKNOWN', $this->createPlatformMock());
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testConvertToDataBaseValueInvalidValue(): void
    {
        self::createType()->convertToDatabaseValue(OtherEnum::BAR(), $this->createPlatformMock());
    }

    public function testTypeUtilizesPlatformStringDeclaration(): void
    {
        $fieldDeclaration = [];

        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->once())
            ->method('getVarcharTypeDeclarationSQL')
            ->with($fieldDeclaration);

        self::createType()->getSQLDeclaration($fieldDeclaration, $platform);
    }

    public function testTypeUtilizesDefaultStrategy(): void
    {
        self::assertSame(
            'TWO',
            self::createType()->convertToDatabaseValue(TestEnum::TWO(), $this->createPlatformMock())
        );
    }

    public function testTypeUtilizesGivenStrategy(): void
    {
        self::assertSame(
            'two',
            self::createType(new LowercaseNamingStrategy())
                ->convertToDatabaseValue(TestEnum::TWO(), $this->createPlatformMock())
        );
    }

    private function createPlatformMock(): AbstractPlatform
    {
        return $this->createMock(AbstractPlatform::class);
    }

    private static function createType(NamingStrategyInterface $strategy = null): EnumType
    {
        if (!Type::hasType(self::TESTED_TYPE)) {
            Type::addType(self::TESTED_TYPE, EnumType::class);
        }

        /** @var EnumType $type */
        $type = Type::getType(self::TESTED_TYPE);
        $type->setName(self::TESTED_TYPE);
        $type->setFqcn(TestEnum::class);
        if ($strategy) {
            $type->setStrategy($strategy);
        }

        return $type;
    }
}

/**
 * @method static static BAR
 */
final class OtherEnum extends Enum
{
    protected const BAR = 'bar';
}
