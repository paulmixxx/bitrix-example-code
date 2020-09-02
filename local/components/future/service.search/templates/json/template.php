<?php

/** @global $APPLICATION */

/** @var $arResult */

$APPLICATION->RestartBuffer();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

echo json_encode($arResult, JSON_UNESCAPED_UNICODE);

die();
