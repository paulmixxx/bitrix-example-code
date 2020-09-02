<?php

/** @global $APPLICATION */
/** @global $USER */

use Future\Commands\EquipmentRequest\Update\Command;
use Future\Commands\EquipmentRequest\Update\Handler;
use Future\Models\JsonResponse;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

$APPLICATION->RestartBuffer();

try {
    \Future\Middleware\Cors::handle();
    \Future\Middleware\Auth::handle();

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
        if (property_exists(Command::class, $key)) {
            $command->$key = $item;
        }
    }

    $handler->handle($command);

    if ($command->SAVE_FINAL === "Y") {
        $commandOrder = new \Future\Commands\EquipmentOrder\Add\Command();
        $handlerOrder = new \Future\Commands\EquipmentOrder\Add\Handler($USER);

        $commandOrder->ELEMENT_ID = $command->ID;
        $orderId = $handlerOrder->handle($commandOrder);

        echo new JsonResponse(
            [
                "success" => true,
                "id" => (int) $command->ID,
                "orderId" => (int) $orderId
            ],
            201
        );
        exit();
    }

    echo new JsonResponse(
        [
            "success" => true,
        ],
        200
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
