<?php

/** @global $APPLICATION */
/** @global $USER */

use Future\Commands\SparePartsRequest\Add\Command;
use Future\Commands\SparePartsRequest\Add\Handler;
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

    $id = $handler->handle($command);

    if ($command->SAVE_FINAL === "Y") {
        $commandOrder = new \Future\Commands\SparePartsOrder\Add\Command();
        $handlerOrder = new \Future\Commands\SparePartsOrder\Add\Handler($USER);

        $commandOrder->ELEMENT_ID = $id;
        $orderId = $handlerOrder->handle($commandOrder);

        echo new JsonResponse(
            [
                "success" => true,
                "id" => (int) $id,
                "orderId" => (int) $orderId
            ],
            201
        );
        exit();
    }

    echo new JsonResponse(
        [
            "success" => true,
            "id" => (int) $id
        ],
        201
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
