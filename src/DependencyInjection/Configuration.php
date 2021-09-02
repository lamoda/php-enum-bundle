<?php

namespace Lamoda\EnumBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const STRATEGIES = [
        'identical',
        'lowercase',
    ];

    /** {@inheritdoc} */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $name = 'lamoda_enum';
        if (method_exists(TreeBuilder::class, 'root')) {
            $builder = new TreeBuilder();
            $root = $builder->root($name);
        } else {
            $builder = new TreeBuilder($name);
            $root = $builder->getRootNode();
        }

        $root
            ->children()
                ->booleanNode('enum_name_type_mapping')
                    ->defaultValue(false)
                ->end()
                ->enumNode('default_strategy')
                    ->values(self::STRATEGIES)
                    ->defaultValue(self::STRATEGIES[0])
                ->end()
            ->end();

        $this->configureEnumNodes($root);

        return $builder;
    }

    private function configureEnumNodes(ArrayNodeDefinition $parent): void
    {
        $types = $parent->children()->arrayNode('dbal_types');

        $dbalTypeProto = $types->prototype('array');
        $dbalTypeProto->addDefaultsIfNotSet();
        $dbalTypeProto
            ->beforeNormalization()
            ->ifString()
            ->then(
                function (string $v) {
                    return ['class' => $v];
                }
            )
            ->end();
        $dbalTypeProto
            ->children()
                ->scalarNode('class')
                    ->isRequired()
                    ->example('My\Enum')
                ->end()
                ->enumNode('strategy')
                    ->values(self::STRATEGIES)
                ->end()
            ->end();
    }
}
