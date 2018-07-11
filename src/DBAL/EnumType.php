<?php

namespace Lamoda\EnumBundle\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Lamoda\EnumBundle\Naming\IdenticalNamingStrategy;
use Lamoda\EnumBundle\Naming\NamingStrategyInterface;
use Paillechat\Enum\Enum;
use Paillechat\Enum\Exception\EnumException;

final class EnumType extends Type
{
    /** @var string */
    private $fqcn;
    /** @var string */
    private $name;
    /** @var NamingStrategyInterface */
    private $strategy;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setFqcn(string $fqcn): void
    {
        $this->fqcn = $fqcn;
    }

    public function setStrategy(NamingStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    private function getStrategy(): NamingStrategyInterface
    {
        if (!$this->strategy) {
            $this->strategy = new IdenticalNamingStrategy();
        }

        return $this->strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var Enum $fqcn */
        $fqcn = $this->fqcn;

        try {
            return $fqcn::createByName($this->getStrategy()->toEnumName($value));
        } catch (EnumException $e) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_a($value, $this->fqcn)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $this->getStrategy()->fromEnumName((string) $value);
    }

    /** {@inheritdoc} */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /** {@inheritdoc} */
    public function getName(): string
    {
        return $this->name;
    }
}
