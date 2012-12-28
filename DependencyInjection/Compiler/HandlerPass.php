<?php

/*
 * This file is part of the SpFixtureDumperBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HandlerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $handlerRegistry = $container->getDefinition('sp_fixture_dumper.handler_registry');
        foreach ($container->findTaggedServiceIds('sp_fixture_dumper.handler') as $serviceId => $tag) {
            $handlerRegistry->addMethodCall('addSubscribingHandler', array(new Reference($serviceId)));
        }
    }

}
