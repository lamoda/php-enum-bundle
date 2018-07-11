<?php

namespace Lamoda\EnumBundle\Naming;

final class IdenticalNamingStrategy implements NamingStrategyInterface
{
    public function fromEnumName(string $enumName): string
    {
        return $enumName;
    }

    public function toEnumName(string $value): string
    {
        return $value;
    }
}
