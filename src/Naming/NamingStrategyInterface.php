<?php

namespace Lamoda\EnumBundle\Naming;

interface NamingStrategyInterface
{
    public function fromEnumName(string $enumName): string;

    public function toEnumName(string $value): string;
}
