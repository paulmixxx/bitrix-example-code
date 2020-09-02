<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var $arParams */
/** @var $arResult */

use Bitrix\Main\Application;
use Future\Models\ServicesFilter;

/**
 * Предопределенные символьные коды инфоблоков
 */
$defaultIblockCodes = [
    "service" => "content:services",
    "product" => "catalog:catalog",
    "category" => "catalog:categor",
];

try {
    /**
     * Получаем ID инфоблоков по их символьным кодам
     */
    $iblocksID = array();
    foreach ($defaultIblockCodes as $name => $code) {
        $iblocksID[$name] = $code;
        if (!empty($arParams["IBLOCK_CODES"][$name])) {
            $iblocksID[$name] = $code;
        }
        $iblocksID[$name] = iblock_id($iblocksID[$name]);
    }

    $request = Application::getInstance()->getContext()->getRequest();

    $serviceFilter = new ServicesFilter(
        $request,
        $iblocksID["service"],
        $iblocksID["product"],
        $iblocksID["category"]
    );
    $currentStep = $serviceFilter->getCurrentStep();

    switch ($currentStep) {
        case "city":
        case "category":
        case "query":
            /**
             * Фильтруем список сервисов и их категорий по городу
             */
            $data = $serviceFilter->getServices($serviceFilter->request("city"));
            $arResult["SERVICES"] = $data["SERVICES"];
            $arResult["CATEGORIES"] = $serviceFilter->getCategories();
            break;
//        case "category":
//            /**
//             * Фильтруем список сервисов по городу и категории
//             */
//            $data = $serviceFilter->getServices(
//                $serviceFilter->request("city"),
//                $serviceFilter->request("category")
//            );
//            $arResult["SERVICES"] = $data["SERVICES"];
//            break;
//        case "query":
//            /**
//             * Фильтруем список товаров по городу, категории и (имени или артиклу)
//             */
//            $products = $serviceFilter->getProduct($serviceFilter->request("query"));
//            $arResult["SERVICES"] = array();
//            if (in_array($serviceFilter->request("category"), $products["CATEGORIES"])) {
//                $data = $serviceFilter->getServices(
//                    $serviceFilter->request("city"),
//                    $serviceFilter->request("category")
//                );
//                $arResult["SERVICES"] = $data["SERVICES"];
//            }
            break;
        default:
            /**
             * Если никаких параметров не передали,
             * выводим список городов
             */
            $arResult["CITIES"] = $serviceFilter->getCities();
    }
} catch (Throwable $e) {
    $arResult["ERRORS"][] = $e->getMessage();
} catch (Exception $e) {
    $arResult["ERRORS"][] = $e->getMessage();
}

$this->IncludeComponentTemplate();
