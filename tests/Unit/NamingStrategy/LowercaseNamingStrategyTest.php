<?php

namespace Lamoda\EnumBundle\Tests\Unit\NamingStrategy;

use Lamoda\EnumBundle\Naming\LowercaseNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \Lamoda\EnumBundle\Naming\LowercaseNamingStrategy
 */
final class LowercaseNamingStrategyTest extends TestCase
{
    public function testConversionIdempotent(): void
    {
        $strat = new LowercaseNamingStrategy();

        $name = 'ENUM_CONST';

        self::assertSame($name, $strat->toEnumName($strat->fromEnumName($name)));
    }

    public function testConversions(): void
    {
        $strat = new LowercaseNamingStrategy();

        self::assertSame('value', $strat->fromEnumName('VALUE'));
        self::assertSame('VALUE', $strat->toEnumName('value'));
    }
}
