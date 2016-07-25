<?php
namespace Vnecoms\Console\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateModuleCommand extends Command
{

    protected function configure()
    {
        $this->setName('vnecoms::create')
            ->setDescription('Create module Vnecoms.')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED,
                'Name space'
            )
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Module Name'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('namespace');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }
}