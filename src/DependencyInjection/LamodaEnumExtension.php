<?php

namespace Lamoda\EnumBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Lamoda\EnumBundle\DBAL\EnumTypeInitializer;
use Lamoda\EnumBundle\Naming\IdenticalNamingStrategy;
use Lamoda\EnumBundle\Naming\LowercaseNamingStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class LamodaEnumExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration(new Configuration(), $configs);

        $typeInitializer = $container->register(EnumTypeInitializer::class, EnumTypeInitializer::class);
        $typeInitializer->setPublic(true);

        $container->register(IdenticalNamingStrategy::class, IdenticalNamingStrategy::class);
        $container->register(LowercaseNamingStrategy::class, LowercaseNamingStrategy::class);

        foreach ($configs['dbal_types'] ?? [] as $name => $typeConfig) {
            $fqcn = $typeConfig['class'];
            $strategy = $this->getStrategy($typeConfig['strategy'] ?? $configs['default_strategy']);
            $typeInitializer->addMethodCall('initialize', [$name, $fqcn, $strategy, $configs['enum_name_type_mapping']]);
        }
    }

    private function getStrategy(?string $name): ?Reference
    {
        $strategy = null;
        switch ($name) {
            case 'lowercase':
                $strategy = new Reference(LowercaseNamingStrategy::class);
                break;
            case 'identical':
                $strategy = new Reference(IdenticalNamingStrategy::class);
                break;
        }

        return $strategy;
    }
}
