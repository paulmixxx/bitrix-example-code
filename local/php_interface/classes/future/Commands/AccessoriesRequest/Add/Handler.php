<?php

namespace Future\Commands\AccessoriesRequest\Add;

use Bitrix\Main\Loader;
use CIBlockElement;
use CIBlockPropertyEnum;
use CUser;
use Exception;
use ReflectionClass;

class Handler
{
    /**
     * @var CUser
     */
    private $user;
    /**
     * @var CIBlockElement
     */
    private $CIBlockElement;

    public function __construct(CUser $user)
    {
        Loader::includeModule("iblock");

        $this->user = $user;
        $this->CIBlockElement = new CIBlockElement();
    }

    /**
     * @param Command $command
     * @return bool|int
     * @throws Exception
     */
    public function handle(Command $command)
    {
        if (!$this->user->IsAuthorized()) {
            throw new Exception("Unauthorized.", 401);
        }

        $properties = [];

        $reflect = new ReflectionClass(Command::class);
        $props = $reflect->getProperties();
        foreach ($props as $prop) {
            $propName = $prop->name;
            switch ($propName) {
                case "ACCESSORIES":
                    $tmp = [];
                    foreach ($command->$propName as $item) {
                        $item["COUNT"] = (int) $item["COUNT"];
                        if (
                            !empty($item["VENDOR_CODE"])
                            && !empty($item["NAME"])
                            && ($item["COUNT"] >= 1)
                        ) {
                            $tmp[] = $item;
                        }
                    }
                    if (!empty($tmp)) {
                        $properties[$propName] = $tmp;
                    } else {
                        throw new Exception("Bad Request.", 400);
                    }
                    break;
                default:
                    $properties[$propName] = $command->$propName;
            }
        }

        $propertyEnums = CIBlockPropertyEnum::GetList(
            [
                "DEF" => "DESC",
                "SORT" => "ASC"
            ],
            [
                "IBLOCK_ID" => iblock_id("catalog:accessories-lk"),
                "CODE" => "STATUS"
            ]
        );
        while ($enumFields = $propertyEnums->GetNext()) {
            $arResult["ENUM_LIST"][$enumFields["PROPERTY_CODE"]][$enumFields["XML_ID"]] = $enumFields;
        }

        $vendorCodeList = array_column($properties["ACCESSORIES"], "VENDOR_CODE");
        $sparePartsList = [];
        $arSelect = [];
        $arFilter = [
            "IBLOCK_ID" => iblock_id("catalog:accessories"),
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_VENDOR_CODE" => $vendorCodeList
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $sparePartsList[$arProps["VENDOR_CODE"]["VALUE"]] = $arFields;
            $sparePartsList[$arProps["VENDOR_CODE"]["VALUE"]]["PROPERTIES"] = $arProps;
        }

        foreach ($vendorCodeList as $vendorCode) {
            if (!array_key_exists($vendorCode, $sparePartsList)) {
                throw new Exception("Аксессуара с артикулом '$vendorCode' не существует.", 404);
            }
        }

        $properties["USER"] = $this->user->GetID();
        $properties["STATUS"] =
            $command->SAVE_FINAL === 'Y'
                ? $arResult["ENUM_LIST"]["STATUS"]["in_progress"]["ID"]
                : $arResult["ENUM_LIST"]["STATUS"]["draft"]["ID"];

        $arLoadProductArray = array(
            'MODIFIED_BY' => $this->user->GetID(),
            'CREATED_BY' => $this->user->GetID(),
            'IBLOCK_ID' => iblock_id("catalog:accessories-lk"),
            'PROPERTY_VALUES' => $properties,
            'NAME' => 'Договор ' . $command->PHONE,
            'ACTIVE' => 'Y',
            'DATE_ACTIVE_FROM' => date("d.m.Y H:i:s"),
        );

        if ($elementId = $this->CIBlockElement->Add($arLoadProductArray)) {
            return $elementId;
        } else {
            throw new Exception($this->CIBlockElement->LAST_ERROR);
        }
    }
}
