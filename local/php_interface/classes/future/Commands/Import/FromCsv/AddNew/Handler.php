<?php

namespace Future\Commands\Import\FromCsv\AddNew;

use CIBlockElement;
use CModule;
use CUtil;
use Exception;
use ParseCsv\Csv;
use Webmozart\Assert\Assert;

class Handler
{
    /**
     * @var CIBlockElement
     */
    private $CIBlockElement;
    /**
     * @var Csv
     */
    private $csv;

    public function __construct()
    {
        $this->loadModules();
        $this->CIBlockElement = new CIBlockElement();
        $this->csv = new Csv();
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command)
    {
        $filePath = APP_PATH . $command->file;
        $iblockCode = trim($command->iblockCode);

        Assert::file($filePath);
        Assert::minLength($iblockCode, 1, "You have not passed Iblock Code argument.");

        $this->csv->delimiter = $command->delimiter ?: ",";
        $this->csv->parse($filePath);

        $iblockId = iblock_id($iblockCode);
        $result = [];

        foreach ($this->csv->data as $row) {
            $prop = [];

            foreach ($row as $k => $value) {
                $match = null;
                if (preg_match("/^PROP_(.+)$/u", $k, $match)) {
                    $prop[$match[1]] = $value;
                }
            }

            $name = $row["NAME"];
            $code = Cutil::translit($name, "ru");

            $arLoadProductArray = [
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => $iblockId,
                "PROPERTY_VALUES" => $prop,
                "NAME" => $name,
                "CODE" => $code,
                "ACTIVE" => "Y",
            ];

            if ($productId = $this->CIBlockElement->Add($arLoadProductArray)) {
                $result[] = "New ID: " . $productId . " - [" . $name . "]";
            } else {
                $result[] = "Error: " . $this->CIBlockElement->LAST_ERROR;
            }
        }

        return $result;
    }

    private function loadModules()
    {
        CModule::IncludeModule("iblock");
    }
}
