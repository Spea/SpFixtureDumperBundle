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
use Sp\FixtureDumper\AbstractDumper;
use Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface;
use Sp\FixtureDumper\Generator\AbstractGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'The path where the fixtures should be dumped to.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'The format to use for dumping fixtures', 'class')
            ->addOption('single-file', 'sf', InputOption::VALUE_NONE, 'Whether or not to dump all fixtures in one single file.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp()
    {
        $help = parent::getHelp();
        if (null !== $help) {
            return $help;
        }

        /** @var $generators \PhpCollection\Map */
        $generators = $this->getContainer()->get('sp_fixture_dumper.generators_map');

        $help = <<<EOT
The <info>%1\$s</info> command dumps fixtures to a directory from your existing entities/documents:

  <info>./app/console %1\$s /path/to/fixtures</info>

The path argument can include parameters (like <info>%%kernel.root_dir%%</info>) and you can use
the bundle annotation (<info>@AcmeDemoBundle/DataFixtures/Fixtures</info>)

If you want to put all fixtures in one file you can use the <info>--single-file</info> option:

  <info>./app/console %1\$s --path=/path/to/fixtures --single-file</info>

You can also use different formats for the dumped fixtures (Available formats: <comment>%2\$s</comment>)

  <info>./app/console %1\$s --format=yml --single-file /path/to/fixtures</info>

Some formats require you to enter extra options, like the <comment>class</comment> format where
you have to specify the namespace.
EOT;

        return sprintf($help, $this->getName(), implode(', ', $generators->keys()));
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format');
        /** @var $generators \PhpCollection\Map */
        $generators = $this->getContainer()->get('sp_fixture_dumper.generators_map');
        $options = $this->askForOptions($output, $generators->get($format)->get());

        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation($output, '<question>Careful, all previously generated fixtures will be overridden. Do you want to continue Y/N ?</question>', false)) {
            return;
        }

        /** @var $handlerRegistry \Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface */
        $handlerRegistry = $this->getContainer()->get('sp_fixture_dumper.handler_registry');

        $dumper = $this->getDumper($input, $handlerRegistry, $generators);
        $dumper->setDumpMultipleFiles(!$input->getOption('single-file'));

        $path = $this->parsePath($input->getArgument('path'));
        $dumper->dump($path, $format, $options);
    }

    /**
     * Asks the user to enter a value for every required option for the used generator.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Sp\FixtureDumper\Generator\AbstractGenerator     $generator
     *
     * @return array
     */
    protected function askForOptions(OutputInterface $output, AbstractGenerator $generator)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $options = array();
        foreach ($generator->getRequiredOptions() as $requiredOption) {
           $options[$requiredOption] = $dialog->ask($output, sprintf('<question>Please enter a value for the required option "%s": </question>', $requiredOption));
        }

        return $options;
    }

    /**
     * Parse the path and replace parameters or bundle annotation.
     *
     * @param string $path
     *
     * @return mixed|string
     */
    protected function parsePath($path)
    {
        preg_match_all('#%(.*?)%#i', $path, $matches);
        $search = array();
        $replace = array();
        foreach ($matches[1]  as $key => $parameter) {
            if ($this->getContainer()->hasParameter($parameter)) {
                $search[] = $matches[0][$key];
                $replace[] = $this->getContainer()->getParameter($parameter);
            }
        }

        $path = str_replace($search, $replace, $path);
        if ('@' == $path[0] && false !== strpos($path, '/')) {
            $bundle = substr($path, 1);
            if (false !== $pos = strpos($bundle, '/')) {
                $bundle = substr($bundle, 0, $pos);
            }
            $relativePath = str_replace('@'. $bundle. '/', '', $path);

            /** @var $kernel KernelInterface */
            $kernel = $this->getContainer()->get('kernel');
            $path = $kernel->getBundle($bundle)->getPath() .'/'. $relativePath;
        }

        return $path;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface              $input
     * @param \Sp\FixtureDumper\Converter\Handler\HandlerRegistryInterface $handlerRegistry
     * @param \PhpCollection\Map                                           $generators
     *
     * @return AbstractDumper
     */
    abstract protected function getDumper(InputInterface $input, HandlerRegistryInterface $handlerRegistry, Map $generators);

}
