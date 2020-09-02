<?php

namespace Future\Commands\WarrantyRepair\Update;

use Bitrix\Main\Loader;
use CIBlockPropertyEnum;
use CUser;
use CIBlockElement;
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

        if (
            !empty($element["PROPERTIES"]["STATUS"]["VALUE"])
            && $element["PROPERTIES"]["STATUS"]["VALUE_XML_ID"] != "draft"
        ) {
            throw new DomainException("Редактирование запрещено.", 400);
        }

        $arSelect = [
            "ID",
            "NAME",
            "IBLOCK_CODE"
        ];
        $arFilter = [
            "IBLOCK_ID" => [
                iblock_id("catalog:customer-requirements"),
                iblock_id("catalog:violation-rules-operation"),
                iblock_id("catalog:completed-work"),
                iblock_id("catalog:malfunction-code"),
                iblock_id("catalog:malfunction"),
                iblock_id("catalog:disposal-sc"),
            ],
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        while ($ob = $res->Fetch()) {
            $arResult["IBLOCK_ITEMS"][$ob["ID"]] = $ob;
            $arResult["IBLOCK_LISTS"][$ob["IBLOCK_CODE"]][$ob["ID"]] = $ob;
        }

        $propertyEnums = CIBlockPropertyEnum::GetList(
            [
                "DEF" => "DESC",
                "SORT" => "ASC"
            ],
            [
                "IBLOCK_ID" => iblock_id("catalog:warranty-repair"),
                "CODE" => [
                    "STATUS",
                    "ADD_DISPOSAL_IN_REPORT"
                ]
            ]
        );
        while ($enumFields = $propertyEnums->GetNext()) {
            $arResult["ENUM_LIST"][$enumFields["PROPERTY_CODE"]][$enumFields["XML_ID"]] = $enumFields;
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
                case "DATE_ACCEPTANCE":
                case "DATE_DIAGNOSTIC":
                case "DATE_SPARE_PARTS_ORDER":
                case "DATE_COMPLETION_WORK":
                case "DATE_RETURN_CUSTOMER":
                case "DATE_SALE":
                    $date = $command->$propName;
                    $properties[$propName] = $date ? date("d.m.Y", strtotime($date)) : null;
                    break;
                case "CUSTOMER_REQUIREMENTS":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["customer-requirements"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "VIOLATION_RULES_OPERATION":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["violation-rules-operation"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "COMPLETED_WORK":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["completed-work"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "PROBLEM_CODE":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["malfunction-code"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "PROBLEM":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["malfunction"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "DISPOSAL_IN_SC":
                    $value = $command->$propName;
                    $list = $arResult["IBLOCK_LISTS"]["disposal-sc"];
                    $properties[$propName] = $list[$value]["ID"] ?: null;
                    break;
                case "ADD_DISPOSAL_IN_REPORT":
                    $properties[$propName] = $command->$propName == $arResult["ENUM_LIST"][$propName]["Y"]["ID"] ? $command->$propName : null;
                    break;
                default:
                    $properties[$propName] = $command->$propName;
            }
        }

        $properties["USER"] = $this->user->GetID();
        $properties["STATUS"] =
            $command->SAVE_FINAL === 'Y'
                ? $arResult["ENUM_LIST"]["STATUS"]["in_progress"]["ID"]
                : $properties["STATUS"];

        $arLoadProductArray = [
            'MODIFIED_BY' => $this->user->GetID(),
            'PROPERTY_VALUES' => $properties,
            'NAME' => 'Договор ' . $command->CONTRACT,
        ];

        if ($result = $this->CIBlockElement->Update($command->ID, $arLoadProductArray)) {
            return $result;
        } else {
            throw new Exception($this->CIBlockElement->LAST_ERROR);
        }
    }
}
