<?php

namespace Lamoda\EnumBundle\Tests\Functional\Fixtures;

use Paillechat\Enum\Enum;

/**
 * @method static static ONE()
 * @method static static TWO()
 */
final class TestEnum extends Enum
{
    protected const ONE = 'one';
    protected const TWO = 'two';
}
