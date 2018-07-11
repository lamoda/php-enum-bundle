<?php

namespace Lamoda\EnumBundle\Tests\Unit\NamingStrategy;

use Lamoda\EnumBundle\Naming\IdenticalNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\Naming\IdenticalNamingStrategy
 */
final class IdenticalNamingStrategyTest extends TestCase
{
    public function testConversionIdempotent(): void
    {
        $strat = new IdenticalNamingStrategy();

        $name = 'ENUM_CONST';

        self::assertSame($name, $strat->toEnumName($strat->fromEnumName($name)));
    }

    public function testConversions(): void
    {
        $strat = new IdenticalNamingStrategy();

        self::assertSame('VALUE', $strat->fromEnumName('VALUE'));
        self::assertSame('VALUE', $strat->toEnumName('VALUE'));
    }
}
