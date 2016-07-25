<?php
namespace Vnecoms\Console\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\App\State as AppState;

class CreateModuleCommand extends Command
{
    protected $_namespace;
    protected $_module;

    protected $appState;

    public function __construct
    (
        AppState $appState
    )
    {
        $this->appState = $appState;
    }

    protected function configure()
    {
        $this->setName('vnecoms:create_module')
            ->setDescription('Vnecoms create module.')
            ->addArgument(
                'namespace',
                InputArgument::OPTIONAL,
                'Namespace(Default "Vnecoms")'
            )
            ->addArgument(
                'module',
                InputArgument::OPTIONAL,
                'Module name (Default "Demo")'
            )
            ->addOption(
                'fields',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Which colors do you like?',
                array('blue', 'red')
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);
        $this->appState->setAreaCode('vnecoms');

        $namespace = $input->getArgument('namespace');
        if ($namespace) {
            $this->_namespace = $namespace;
        } else {
            $this->_namespace = 'Vnecoms';
        }

        //module name
        $module = $input->getArgument('module');
        if ($module)
        {
            $this->_module = $module;
        }
        else
        {
            $this->_module = 'Demo';
        }

        try {


        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        //end
        $output->write("\n");
        $output->writeln("<info>Create module successfully!</info>");
    }
}