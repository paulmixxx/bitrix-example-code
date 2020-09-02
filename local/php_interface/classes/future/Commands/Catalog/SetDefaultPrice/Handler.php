<?php

namespace Future\Commands\Catalog\SetDefaultPrice;

use CCatalogProduct;
use CIBlockElement;
use CModule;
use CPrice;
use Webmozart\Assert\Assert;

class Handler
{
    /**
     * @var CIBlockElement
     */
    private $CIBlockElement;

    public function __construct()
    {
        $this->loadModules();
        $this->CIBlockElement = new CIBlockElement();
    }

    public function handle(Command $command)
    {
        $iblockCode = $command->iblockCode;
        $price = $command->price;
        $currency = $command->currency;
        $priceType = $command->priceType;

        Assert::minLength($iblockCode, 1, "You have not passed Iblock Code argument.");
        Assert::numeric($price);
        Assert::inArray($currency, ["USD", "RUB"]);
        Assert::integer($priceType);

        $iblockId = iblock_id($iblockCode);
        $result = [];

        $arSelect = [
            "ID"
        ];
        $arFilter = [
            "IBLOCK_ID" => $iblockId,
        ];
        $resList = $this->CIBlockElement->GetList([], $arFilter, false, false, $arSelect);
        while ($ob = $resList->GetNextElement()) {
            $arFields = $ob->GetFields();

            $PRODUCT_ID = $arFields["ID"];
            $arParams = [
                "PRODUCT_ID" => $PRODUCT_ID,
                "CATALOG_GROUP_ID" => $priceType,
                "PRICE" => $price,
                "CURRENCY" => $currency,
            ];

            $res = CPrice::GetList(
                [],
                [
                    "PRODUCT_ID" => $PRODUCT_ID,
                    "CATALOG_GROUP_ID" => $priceType
                ]
            );

            if ($arr = $res->Fetch()) {
                CPrice::Update($arr["ID"], $arParams);
                $result[] = "Update: " . $PRODUCT_ID;
            } else {
                CPrice::Add($arParams);
                $result[] =  "Add: " . $PRODUCT_ID;
            }

            CCatalogProduct::Add(
                [
                    'ID' => $PRODUCT_ID,
                    'QUANTITY' => 20,
                ]
            );
        }

        return $result;
    }

    private function loadModules()
    {
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
    }
}
