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
        $path = array();
        $queue = new \Expose\Queue();
        $records = $queue->pending();

        $filters = new \Expose\FilterCollection();
        $filters->load();

        $manager = new \Expose\Manager($filters);

        foreach ($records as $record) {
            $manager->runFilters($record['data'], $path);
            $queue->markProcessed($record['_id']);
        }

        // not sure what to do here yet
        print_r($manager->getReports());
    }
}