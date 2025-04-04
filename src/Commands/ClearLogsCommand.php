<?php
namespace Explt13\Nosmi\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearLogsCommand extends Command
{
    protected static $defaultName = "logger:clear";

    protected function configure()
    {
        $this->setName(self::$defaultName)
             ->setDescription("Clears all log files.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cleanFolder(__DIR__ . '/../logs');
        $output->writeln('<info>All logs cleared!</info>');
        return Command::SUCCESS;
    }

    protected function cleanFolder($path)
    {
        $dir = glob($path .'/*');
        foreach ($dir as $file) {
            if (is_dir($file)) {
                $this->cleanFolder($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    } 
}