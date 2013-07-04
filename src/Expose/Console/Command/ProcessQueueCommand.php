<?php
namespace Expose\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ProcessQueueCommand extends Command
{
    /**
     * Manager object instance
     * @var \Expose\Manager
     */
    private $manager = null;

    /**
     * Queue object instance
     * @var \Expose\Queue
     */
    private $queue = null;

    protected function configure()
    {
        $this->setName('process-queue')
            ->setDescription('Process the outstanding items in the current queue')
            ->setDefinition(array(
                new InputOption('list', 'list', InputOption::VALUE_NONE, 
                    'List the current items in the queue'),
                new InputOption('export-file', 'export-file', InputOption::VALUE_NONE, 
                    'The file path to write out results to'),
                new InputOption('queue-type', 'queue-type', InputOption::VALUE_NONE,
                    'Queue type (Ex. "mysql" or "mongo"'),
                new InputOption('queue-connect', 'queue-connect', InputOption::VALUE_NONE,
                    'Queue connection information: user:pass@host'),
                new InputOption('notify-email', 'notify-email', InputOption::VALUE_NONE,
                    'Email address to use for notifications')
            ))
            ->setHelp(
                'This command lets you process and execute filters on the user input'
                .' currently in the queue');
    }

    /**
     * Get the Manager instance (or make a new one)
     * 
     * @return \Expose\Manager instance
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            $filters = new \Expose\FilterCollection();
            $filters->load();

            $manager = new \Expose\Manager($filters);
            $this->manager = $manager;
        } else {
            $manager = $this->manager;
        }
        return $manager;
    }

    /**
     * Get the Queue instance (or make a new one)
     * 
     * @return \Expose\Queue instance
     */
    protected function getQueue()
    {
        if ($this->queue === null) {
            $queue = new \Expose\Queue();
            $this->queue = $queue;
        } else {
            $queue = $this->queue;
        }
        return $queue;
    }

    /**
     * Set the queue object for the current execution
     * 
     * @param \Expose\Queue $queue instance
     */
    protected function setQueue($queue)
    {
        $this->queue = $queue;
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
	$dsn = $this->getOption('dsn');
	$notifyEmail = $this->getOption('notify-email');

        $manager = $this->getManager();
	if ($notifyEmail !== null) {
		$notify = new \Expose\Notify\Email();
		$notify->setToAddress($notifyEmail);
		$manager->setNotify($notify);
	}

        $queue = $this->getQueue();

        if ($input->getOption('list') !== false) {
            $output->writeln('<info>Current Queue Items Pending</info>');
            $this->listQueue($manager, $queue);
            return true;
        }

        // by default process the current queue
        $reports = $this->processQueue($output);
        if (count($reports) == 0) {
            return;
        }

        $exportFile = $input->getOption('export-file');
        if ($exportFile !== false) {
            $output->writeln('<info>Outputting results to file '.$exportFile.'</info>');
            file_put_contents(
                $exportFile, 
                '['.date('m.d.Y H:i:s').'] '.json_encode($reports),
                FILE_APPEND
            );
        } else {
            echo json_encode($reports);
        }
    }

    /**
     * Run the queue processing
     * 
     * @param OutputInterface $output Reference to output instance
     * @return array Updated reports set
     */
    protected function processQueue(&$output)
    {
        $manager = $this->getManager();
        $queue = $this->getQueue();
        $path = array();
        $records = $queue->pending();

        $output->writeln('<info>'.count($records).' records found.</info>');
        if (count($records) == 0) {
            $output->writeln('<error>No records found to process!</error>');
        } else {
            $output->writeln('<info>Processing '.count($records).' records</info>');
        }

        foreach ($records as $record) {
            $manager->runFilters($record['data'], $path);
            $queue->markProcessed($record['_id']);
        }

        $reports = $manager->getReports();
        foreach ($reports as $index => $report) {
            $reports[$index] = $report->toArray(true);
        }
        return $reports;
    }

    /**
     * Build an adapter based on the type+connection string
     * 
     * @param string $queueType Queue type (Ex. "mongo" or "mysql")
     * @param string $queueConnect Queue connection string (format: user:pass@host)
     * @param string $databaseName Database name
     * @return object Connection adapter
     */
    protected function buildAdapter($queueType, $queueConnect, $databaseName)
    {
        preg_match('/(.+):(.+)@(.+)/', $queueConnect, $connect);
        if (count($connect) < 4) {
            return false;
        }
        list($full, $username, $password, $host) = $connect;

        switch(strtolower($queueType)) {
            case 'mongo':
                if (!extension_loaded('mongo')) {
                    return false;
                }
                $connectString = 'mongodb://'.$username.':'.$password.'@'.$host.'/'.$databaseName;
                $adapter = new \MongoClient($connectString);
                break;
            case 'mysql':
                if (!extension_loaded('mysqli')) {
                    return false;
                }
                $adapter = new mysqli($host, $username, $password, $databaseName);
                break;
        }

        return $adapter;
    }
}
