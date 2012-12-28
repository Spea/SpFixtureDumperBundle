<?php

/*
 * This file is part of the SpFixtureDumperBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\FixtureDumperBundle\Command;

use PhpCollection\Map;
use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;
use Sp\FixtureDumper\ORMDumper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ORMDumpCommand extends AbstractDumpCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('sp:fixture-dumper:orm')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->setDescription('Creates fixtures from ORM entities')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDumper(InputInterface $input, HandlerRegistryInterface $handlerRegistry, Map $generators)
    {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager($input->getOption('em'));

        return new ORMDumper($em, $handlerRegistry, $generators);
    }
}
