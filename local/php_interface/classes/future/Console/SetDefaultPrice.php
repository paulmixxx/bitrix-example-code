<?php

namespace Future\Console;

use Exception;
use Future\Commands\Catalog\SetDefaultPrice\Command as SetDefaultPriceCommand;
use Future\Commands\Catalog\SetDefaultPrice\Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetDefaultPrice extends Command
{
    protected static $defaultName = 'catalog:set-default-price';
    protected static $price = 5000;
    protected static $currency = "RUB";
    protected static $priceType = 1;

    protected function configure()
    {
        $this
            ->addArgument('iblockCode', InputArgument::REQUIRED, 'IBlock Code in format IBLOCK_TYPE:IBLOCK_CODE.')
            ->addArgument('price', InputArgument::OPTIONAL, 'Set price, Default: ' . self::$price)
            ->addArgument('currency', InputArgument::OPTIONAL, 'Set currency, Default: ' . self::$currency)
            ->addArgument('priceType', InputArgument::OPTIONAL, 'Set priceType, Default: ' . self::$priceType);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $command = new SetDefaultPriceCommand();
            $handler = new Handler();

            $command->iblockCode = $input->getArgument("iblockCode") ?: "";
            $command->price = $input->getArgument("price") ?: static::$price;
            $command->currency = $input->getArgument("currency") ?: static::$currency;
            $command->priceType = $input->getArgument("priceType") ?: static::$priceType;

            $result = $handler->handle($command);

            $output->writeln("");
            $output->writeln("Start...");
            $output->writeln("");

            foreach ($result as $row) {
                $output->writeln($row);
            }
        } catch (Exception $exception) {
            $output->writeln('Error: <error>' . $exception->getMessage() . '</error>');
        }
    }
}
