<?php

use Bitrix\Main\EventManager;

//main

// iblock
EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [\Future\Events\Iblock\RepairListField::class, 'getUserTypeDescription']);

// search
