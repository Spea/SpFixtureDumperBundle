<?php

/*
 * This file is part of the SpFixtureDumperBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumperBundle;

use Sp\FixtureDumperBundle\DependencyInjection\Compiler\GeneratorPass;
use Sp\FixtureDumperBundle\DependencyInjection\Compiler\HandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpFixtureDumperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GeneratorPass());
        $container->addCompilerPass(new HandlerPass());
    }
}
