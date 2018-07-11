<?php

namespace Lamoda\EnumBundle\Tests\Functional;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\DBAL\EnumType;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestEnum;
use Lamoda\EnumBundle\Tests\Functional\Fixtures\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

final class TestTypesAreLoaded extends TestCase
{
    public function testTypesAreLoadedOnKernelBoot(): void
    {
        $this->createKernel();

        self::assertTrue(Type::hasType('test_enum'));
        self::assertTrue(Type::hasType('test_enum_extended'));

        $basic = Type::getType('test_enum');
        $extended = Type::getType('test_enum_extended');

        self::assertInstanceOf(EnumType::class, $basic);
        self::assertInstanceOf(EnumType::class, $extended);

        $platform = $this->createMock(AbstractPlatform::class);
        self::assertSame('ONE', $basic->convertToDatabaseValue(TestEnum::ONE(), $platform));
        self::assertSame('two', $extended->convertToDatabaseValue(TestEnum::TWO(), $platform));
    }

    private function createKernel(): Kernel
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        return $kernel;
    }
}
