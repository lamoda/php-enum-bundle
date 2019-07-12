<?php

namespace Lamoda\EnumBundle\Tests\Functional;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\DBAL\EnumType;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestEnum;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class TestCustomTypeMapping extends TestCase
{
    protected function setUp(): void
    {
        $typeReflectionClass = new \ReflectionClass(Type::class);

        $typesMapProperty = $typeReflectionClass->getProperty('_typesMap');
        $typesMapProperty->setAccessible(true);
        $typesMapProperty->setValue([]);
    }

    /**
     * @param string $configFileName
     * @param string $expectedTestEnumSqlDeclaration
     * @param string $expectedTestEnumExtendedSqlDeclaration
     * @param array  $expectedTestEnumMappedTypes
     * @param array  $expectedTestEnumExtendedMappedTypes
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @dataProvider dataTypeMapping
     */
    public function testTypeMapping(
        string $configFileName,
        string $expectedTestEnumSqlDeclaration,
        string $expectedTestEnumExtendedSqlDeclaration,
        array $expectedTestEnumMappedTypes,
        array $expectedTestEnumExtendedMappedTypes
    ): void {
        $kernel = $this->createKernel($configFileName);

        $basic = Type::getType('test_enum');
        $extended = Type::getType('test_enum_extended');

        $platform = $this->createMock(AbstractPlatform::class);
        $platform->method('getVarcharTypeDeclarationSQL')
            ->willReturn('VARCHAR(255)');

        self::assertSame($expectedTestEnumSqlDeclaration, $basic->getSQLDeclaration([], $platform));
        self::assertSame($expectedTestEnumExtendedSqlDeclaration, $extended->getSQLDeclaration([], $platform));

        self::assertSame($expectedTestEnumMappedTypes, $basic->getMappedDatabaseTypes($platform));
        self::assertSame($expectedTestEnumExtendedMappedTypes, $extended->getMappedDatabaseTypes($platform));

        $kernel->shutdown();
    }

    public function dataTypeMapping(): array
    {
        return [
            [
                'config.yml',
                'VARCHAR(255)',
                'VARCHAR(255)',
                ['test_enum' => 'string'],
                ['test_enum_extended' => 'string'],
            ],
            [
                'config_type_mapping.yml',
                'test_enum',
                'test_enum_extended',
                ['test_enum' => 'test_enum'],
                ['test_enum_extended' => 'test_enum_extended'],
            ],
            [
                'config_type_mapping_false.yml',
                'VARCHAR(255)',
                'VARCHAR(255)',
                ['test_enum' => 'string'],
                ['test_enum_extended' => 'string'],
            ],
        ];
    }

    private function createKernel(string $configFileName): Kernel
    {
        $kernel = new TestKernel('test', true);
        $kernel->setConfigFileName($configFileName);
        $kernel->setCacheDirPostfix(sha1($configFileName));
        $kernel->boot();

        return $kernel;
    }
}
