<?php

namespace Future\Commands\AccessoriesRequest\Update;

use Bitrix\Main\Loader;
use CIBlockElement;
use CIBlockPropertyEnum;
use CUser;
use DomainException;
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

        if (empty($command->ID)) {
            throw new Exception("Id is empty.", 400);
        }

        $element = null;
        $res = CIBlockElement::GetByID($command->ID);

        if ($resObj = $res->GetNextElement()) {
            $element = $resObj->GetFields();
            $element["PROPERTIES"] = $resObj->GetProperties();
        }

        if (!$element || $element["PROPERTIES"]["USER"]["VALUE"] != $this->user->GetID()) {
            throw new Exception("Not Found.", 404);
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

        if (
            !empty($element["PROPERTIES"]["STATUS"]["VALUE"])
            && $element["PROPERTIES"]["STATUS"]["VALUE_XML_ID"] != "draft"
        ) {
            throw new DomainException("Редактирование запрещено.", 400);
        }

        $properties = [];

        foreach ($element["PROPERTIES"] as $key => $prop) {
            switch ($prop["PROPERTY_TYPE"]) {
                case "L":
                    $properties[$key] = $prop["VALUE_ENUM_ID"];
                    break;
                default:
                    $properties[$key] = $prop["VALUE"];
            }
        }

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

        $arLoadProductArray = [
            'MODIFIED_BY' => $this->user->GetID(),
            'PROPERTY_VALUES' => $properties,
            'NAME' => 'Договор ' . $command->PHONE,
        ];

        if ($result = $this->CIBlockElement->Update($command->ID, $arLoadProductArray)) {
            return $result;
        } else {
            throw new Exception($this->CIBlockElement->LAST_ERROR);
        }
    }
}
