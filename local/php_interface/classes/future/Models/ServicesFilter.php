<?php

namespace Future\Models;

use CIBlockElement;
use CModule;

class ServicesFilter
{
    private $request;
    private $serviceIblockID;
    private $productIblockID;
    private $categoryIblockID;
    private $steps = [
        "city",
        "category",
        "query"
    ];
    private $currentStep = null;

    public function __construct(
        $request,
        $serviceIblockID,
        $productIblockID,
        $categoryIblockID
    ) {
        CModule::IncludeModule("iblock");

        $this->request = $request;
        $this->serviceIblockID = $serviceIblockID;
        $this->productIblockID = $productIblockID;
        $this->categoryIblockID = $categoryIblockID;
    }

    public function request($name = null)
    {
        if ($name === null) {
            return $this->request;
        }

        return $this->request->get($name);
    }

    public function getCurrentStep()
    {
        foreach ($this->steps as $step) {
            if (!empty($this->request[$step])) {
                $this->currentStep = $step;
            } else {
                break;
            }
        }

        return $this->currentStep;
    }

    public function getCities()
    {
        $arOrder = array(
            "PROPERTY_CITY" => "ASC"
        );
        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_CITY",
            "PROPERTY_ADDRESS",
            "PROPERTY_PHONE",
        );
        $arFilter = array(
            "IBLOCK_ID" => $this->serviceIblockID,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        );
        $res = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            false,
            $arSelect
        );
        $arCities = array();
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $city = mbUcfirst(trim($arFields["PROPERTY_CITY_VALUE"]));
            $arFields["PROPERTY_CITY_VALUE"] = $city;
            if (!empty($city)) {
                $arCities[$city] = $city;
            }
        }

        return array_keys($arCities);
    }

    public function getServices($city, $category = null)
    {
        $arOrder = array(
            "PROPERTY_CITY" => "ASC"
        );
        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_CITY",
            "PROPERTY_ADDRESS",
            "PROPERTY_PHONE",
            "PROPERTY_CATEGORIES_ID",
        );
        $arFilter = array(
            "IBLOCK_ID" => $this->serviceIblockID,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_CITY" => $city
        );
        if (is_numeric($category) && !empty($category)) {
            $arFilter["PROPERTY_CATEGORIES_ID"] = (int)$category;
        }
        $res = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            false,
            $arSelect
        );
        $result = array();
        $arServices = array();
        $arCategories = array();
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
//            if (!empty($arFields["PROPERTY_CATEGORIES_ID_VALUE"])) {
            if (!empty($arFields["ID"])) {
                $arServices[$arFields["ID"]] = $arFields;
            }
                $arCategories[$arFields["PROPERTY_CATEGORIES_ID_VALUE"]] = $arFields["PROPERTY_CATEGORIES_ID_VALUE"];
//            }
        }

        $result["SERVICES"] = $arServices;
        $result["CATEGORIES_ID"] = $arCategories;

        return $result;
    }

    public function getCategories()
    {
        $result = array();

        // get categories
        $arOrder = array(
            "NAME" => "ASC"
        );
        $arSelect = array(
            "ID",
            "NAME",
        );
        $arFilter = array(
            "IBLOCK_ID" => $this->categoryIblockID,
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
        );
        $res = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            false,
            $arSelect
        );

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $result[$arFields["ID"]] = $arFields["NAME"];
        }

        return $result;
    }

    public function getProduct($query)
    {
        $result = array();
        if (strlen($query) > 0) {
            // get categories
            $arOrder = array(
                "NAME" => "ASC"
            );
            $arSelect = array(
                "ID",
                "NAME",
                "PROPERTY_CATEGOR_ID",
                "PROPERTY_ARTICLE",
            );
            $arFilter = array(
                "IBLOCK_ID" => $this->productIblockID,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                array(
                    "LOGIC" => "OR",
                    array(
                        "NAME" => "%" . $query . "%",
                    ),
                    array(
                        "PROPERTY_ARTICLE" => "%" . $query . "%",
                    ),
                ),
            );

            $res = CIBlockElement::GetList(
                $arOrder,
                $arFilter,
                false,
                false,
                $arSelect
            );

            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $result["ITEMS"][$arFields["ID"]] = $arFields["NAME"];
                $result["CATEGORIES"][$arFields["PROPERTY_CATEGOR_ID_VALUE"]] = $arFields["PROPERTY_CATEGOR_ID_VALUE"];
            }
        }

        return $result;
    }
}
