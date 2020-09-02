<?php

namespace Future\Console\Import;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Future\Commands\Import\FromCsv\AddNew\Command as AddNewCommand;
use Future\Commands\Import\FromCsv\AddNew\Handler;

class AddNewElementFromCsv extends Command
{
    protected static $defaultName = 'catalog:add-new-element-from-csv';

    protected function configure()
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Relative path to file.')
            ->addArgument('iblockCode', InputArgument::REQUIRED, 'IBlock Code in format IBLOCK_TYPE:IBLOCK_CODE.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $command = new AddNewCommand();
            $handler = new Handler();

            $command->file = $input->getArgument("file") ?: "";
            $command->iblockCode = $input->getArgument("iblockCode") ?: "";

            $output->writeln("");
            $output->writeln("Start...");
            $output->writeln("");
            $output->writeln("File path: " . $command->file);
            $output->writeln("Iblock Code: " . $command->iblockCode);
            $output->writeln("");

            $result = $handler->handle($command);

            foreach ($result as $row) {
                $output->writeln($row);
            }
        } catch (Exception $exception) {
            $output->writeln('Error: <error>' . $exception->getMessage() . '</error>');
        }
    }
}
