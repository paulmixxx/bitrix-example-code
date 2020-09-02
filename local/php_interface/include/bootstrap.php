<?php

/**
 *  Bootstrap Application
 */

use Arrilot\BitrixIblockHelper\HLblock;
use Arrilot\BitrixIblockHelper\IblockId;

/**
 * Init .env file
 */
(Dotenv\Dotenv::createImmutable(APP_PATH))->load();

/**
 * Init cache
 */
IblockId::setCacheTime(60 * 60);    // кэшируем ID всех инфоблоков на 60 минут
HLblock::setCacheTime(60 * 60);     // кэшируем данные всех хайлоадблоков на 60 минут
