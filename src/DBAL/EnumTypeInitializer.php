<?php

namespace Lamoda\EnumBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\Naming\NamingStrategyInterface;

final class EnumTypeInitializer
{
    /**
     * @param string $type
     * @param string $fqcn
     * @param NamingStrategyInterface|null $strategy
     * @param bool $enumNameTypeMapping
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function initialize(
        string $type,
        string $fqcn,
        NamingStrategyInterface $strategy = null,
        bool $enumNameTypeMapping = false
    ): void {
        if (Type::hasType($type)) {
            return;
        }

        Type::addType($type, EnumType::class);

        /** @var EnumType $typeInstance */
        $typeInstance = Type::getType($type);
        $typeInstance->setFqcn($fqcn);
        $typeInstance->setName($type);
        if ($strategy) {
            $typeInstance->setStrategy($strategy);
        }
        $typeInstance->setEnumNameTypeMapping($enumNameTypeMapping);
    }
}
