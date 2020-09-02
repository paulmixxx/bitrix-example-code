<?php

/** @global $APPLICATION */
/** @global $USER */

use Bitrix\Main\Loader;
use Future\Models\JsonResponse;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

$APPLICATION->RestartBuffer();

try {
    \Future\Middleware\Cors::handle();
    \Future\Middleware\Auth::handle();

    Loader::includeModule("iblock");

    if (!$vendorCode = trim($_REQUEST["VENDOR_CODE"])) {
        throw new InvalidArgumentException("Не заполнен артикул.");
    }

    $limit = 5;

    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
        "DATE_ACTIVE_FROM",
        "PROPERTY_VENDOR_CODE"
    ];
    $arFilter = [
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_CODE" => "accessories",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
        "PROPERTY_VENDOR_CODE" => $vendorCode . "%",
    ];
    $items = [];
    $res = CIBlockElement::GetList([], $arFilter, false, ["nTopCount" => $limit], $arSelect);
    while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $items[] = [
            "ID" => $arFields["ID"],
            "NAME" => $arFields["NAME"],
            "VENDOR_CODE" => $arProps["VENDOR_CODE"]["VALUE"],
        ];
    }

    echo new JsonResponse(
        [
            "success" => true,
            "items" => $items,
        ]
    );
} catch (Exception $exception) {
    echo new JsonResponse(
        [
            "success" => false,
            "errors" => [
                get_class($exception) => $exception->getMessage()
            ]
        ],
        $exception->getCode() ?: 400
    );
}
