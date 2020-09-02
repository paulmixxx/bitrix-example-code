<?php

/** @global $APPLICATION */
/** @global $USER */

use Future\Commands\WarrantyRepair\Update\Command;
use Future\Commands\WarrantyRepair\Update\Handler;
use Future\Models\JsonResponse;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

$APPLICATION->RestartBuffer();

try {
    \Future\Middleware\Cors::handle();
    \Future\Middleware\Auth::handle();

    $DB->StartTransaction();

    $command = new Command();
    $handler = new Handler($USER);

    foreach ($_POST as $key => $item) {
        if ($key == "PROPERTIES") {
            foreach ($item as $k => $v) {
                if (property_exists(Command::class, $k)) {
                    $command->$k = $v;
                }
            }
        }
        if ($key == "SPARE_PARTS") {
            foreach ($item as $k => $v) {
                if (property_exists(Future\Commands\SparePartsRequest\Update\Command::class, $k)) {
                    $command->$k = $v;
                }
            }
        }
        if (property_exists(Command::class, $key)) {
            $command->$key = $item;
        }
    }

    $files = $_FILES["PROPERTIES"];
    foreach ($files["error"] as $key => $item) {
        if ($item == 0 && property_exists(Command::class, $key)) {
            $command->$key = [
                "name" => $files["name"][$key],
                "type" => $files["type"][$key],
                "tmp_name" => $files["tmp_name"][$key],
                "error" => $files["error"][$key],
                "size" => $files["size"][$key],
            ];
        }
    }

    if (!empty($command->SPARE_PARTS_LK) && is_numeric($command->SPARE_PARTS_LK)) {
        $commandSpareParts = new Future\Commands\SparePartsRequest\Update\Command();
        $handlerSpareParts = new Future\Commands\SparePartsRequest\Update\Handler($USER);

        $commandSpareParts->SPARE_PARTS = array_filter(
            $command->SPARE_PARTS,
            function ($item) {
                return !empty($item["VENDOR_CODE"]) && !empty($item["NAME"]) && (int) $item["COUNT"] >= 1;
            }
        );

        $commandSpareParts->ID = $command->SPARE_PARTS_LK;
        $commandSpareParts->CONTRACT = $_POST["PROPERTIES"]["CONTRACT"];
        $commandSpareParts->SERVICE_NAME = $_POST["PROPERTIES"]["SERVICE_NAME"];
        $commandSpareParts->ADDRESS = $_POST["PROPERTIES"]["ADDRESS"];
        $commandSpareParts->ORDER_OUTFIT = $_POST["PROPERTIES"]["NUMBER_ACT_ACCEPTANCE_ITEM"];
        $commandSpareParts->SAVE_FINAL = $command->SAVE_FINAL;

        $handlerSpareParts->handle($commandSpareParts);
    } else {
        $commandSpareParts = new Future\Commands\SparePartsRequest\Add\Command();
        $handlerSpareParts = new Future\Commands\SparePartsRequest\Add\Handler($USER);

        $commandSpareParts->SPARE_PARTS = array_filter(
            $command->SPARE_PARTS,
            function ($item) {
                return !empty($item["VENDOR_CODE"]) && !empty($item["NAME"]) && (int) $item["COUNT"] >= 1;
            }
        );

        if (!empty($commandSpareParts->SPARE_PARTS)) {
            $commandSpareParts->CONTRACT = $_POST["PROPERTIES"]["CONTRACT"];
            $commandSpareParts->SERVICE_NAME = $_POST["PROPERTIES"]["SERVICE_NAME"];
            $commandSpareParts->ADDRESS = $_POST["PROPERTIES"]["ADDRESS"];
            $commandSpareParts->ORDER_OUTFIT = $_POST["PROPERTIES"]["NUMBER_ACT_ACCEPTANCE_ITEM"];
            $commandSpareParts->SAVE_FINAL = $command->SAVE_FINAL;

            $command->SPARE_PARTS_LK = $handlerSpareParts->handle($commandSpareParts);
        }
    }

    if (
        $command->SAVE_FINAL === "Y"
        && is_numeric($command->SPARE_PARTS_LK)
    ) {
        $commandOrder = new \Future\Commands\SparePartsOrder\Add\Command();
        $handlerOrder = new \Future\Commands\SparePartsOrder\Add\Handler($USER);

        $commandOrder->ELEMENT_ID = $command->SPARE_PARTS_LK;
        $orderId = $handlerOrder->handle($commandOrder);
    }

    $handler->handle($command);

    $DB->Commit();

    echo new JsonResponse(
        [
            "success" => true,
        ],
        200
    );
} catch (Exception $exception) {
    $DB->Rollback();

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
