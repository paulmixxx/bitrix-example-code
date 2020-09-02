<?php

/** @global $APPLICATION */
/** @global $USER */

use Future\Commands\WarrantyRepairPhoto\Delete\Command;
use Future\Commands\WarrantyRepairPhoto\Delete\Handler;
use Future\Models\JsonResponse;

require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php';

$APPLICATION->RestartBuffer();

try {
    \Future\Middleware\Cors::handle();
    \Future\Middleware\Auth::handle();

    $command = new Command();
    $handler = new Handler($USER);

    foreach ($_POST as $key => $item) {
        if (property_exists(Command::class, $key)) {
            $command->$key = $item;
        }
    }

    $handler->handle($command);

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
