<?php

/** @global $APPLICATION */
/** @global $USER */

use Future\Commands\SparePartsOrder\Add\Command;
use Future\Commands\SparePartsOrder\Add\Handler;
use Future\Models\JsonResponse;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

$APPLICATION->RestartBuffer();

try {
    \Future\Middleware\Cors::handle();
    \Future\Middleware\Auth::handle();

    $command = new Command();
    $handler = new Handler($USER);

    $command->ELEMENT_ID = $_POST["ID"];
    $orderId = $handler->handle($command);

    echo new JsonResponse(
        [
            "success" => true,
            "id" => (int) $command->ELEMENT_ID,
            "orderId" => (int) $orderId
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
