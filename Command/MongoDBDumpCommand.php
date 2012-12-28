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
use Sp\FixtureDumper\MongoDBDumper;
use Sp\FixtureDumper\ORMDumper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MongoDBDumpCommand extends AbstractDumpCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('sp:fixture-dumper:mongodb')
            ->addOption('dm', null, InputOption::VALUE_REQUIRED, 'The document manager to use for this command.')
            ->setDescription('Creates fixtures from MongoDB documents')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDumper(InputInterface $input, HandlerRegistryInterface $handlerRegistry, Map $generators)
    {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine_mongodb');
        $dm = $doctrine->getManager($input->getOption('dm'));

        return new MongoDBDumper($dm, $handlerRegistry, $generators);
    }
}
