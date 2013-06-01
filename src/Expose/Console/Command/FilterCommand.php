<?php
namespace Expose\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FilterCommand extends Command
{
    protected function configure()
    {
        $this->setName('filter')
            ->setDescription('Sample test')
            ->setDefinition(array(
                new InputOption('id', 'id', InputOption::VALUE_NONE, 'Filter ID to describe')
            ))
            ->setHelp('this is a <info>test</info>');
    }

    /**
     * Execute the filter command
     * 
     * @param  InputInterface  $input  Input object
     * @param  OutputInterface $output Output object
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $col = new \Expose\FilterCollection();
        $col->load();
        $filters = $col->getFilterData();

        $id = $input->getOption('id');
        if ($id !== false) {
            if (array_key_exists($id, $filters)) {
                $detail = "[".$id."] ".$filters[$id]->getDescription()."\n";
                $detail .= "\tRule: ".$filters[$id]->getRule()."\n";
                $detail .= "\tTags: ".implode(', ', $filters[$id]->getTags())."\n";
                $detail .= "\tImpact: ".$filters[$id]->getImpact()."\n";

                $output->writeLn($detail);
            } else {
                $output->writeLn('Filter ID '.$id.' not found!');
            }
            return;
        }

        foreach ($filters as $filter) {
            echo $filter->getId().': '. $filter->getDescription()."\n";
        }
        return;
    }
}

?>