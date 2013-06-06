<?php
namespace Expose\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ProcessQueueCommand extends Command
{
    protected function configure()
    {
        $this->setName('process-queue')
            ->setDescription('Process the outstanding items in the current queue')
            ->setDefinition(array(
                //new InputOption('id', 'id', InputOption::VALUE_NONE, 'Filter ID to describe')
            ))
            ->setHelp(
                'This command lets you process and execute filters on the user input'
                .' currently in the queue');
    }

    /**
     * Execute the process-queue command
     * 
     * @param  InputInterface  $input  Input object
     * @param  OutputInterface $output Output object
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('Not implemented yet');
    }
}