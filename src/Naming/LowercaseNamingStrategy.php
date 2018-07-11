<?php

namespace Lamoda\EnumBundle\Naming;

final class LowercaseNamingStrategy implements NamingStrategyInterface
{
    public function fromEnumName(string $enumName): string
    {
        return strtolower($enumName);
    }

    public function toEnumName(string $value): string
    {
        return strtoupper($value);
    }
}
