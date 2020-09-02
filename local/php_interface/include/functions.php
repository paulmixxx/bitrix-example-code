<?php

/**
 * Determine if a given string starts with a given substring.
 *
 * @param  string  $haystack
 * @param  string|array  $needles
 * @return bool
 */
function strStartsWith($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
            return true;
        }
    }

    return false;
}

/**
 * Determine if a given string ends with a given substring.
 *
 * @param  string  $haystack
 * @param  string|array  $needles
 * @return bool
 */
function strEndsWith($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if (substr($haystack, -strlen($needle)) === (string) $needle) {
            return true;
        }
    }

    return false;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
    return $value instanceof Closure ? $value() : $value;
}

/**
 * Gets the value of an environment variable.
 *
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (strlen($value) > 1 && strStartsWith($value, '"') && strEndsWith($value, '"')) {
        return substr($value, 1, -1);
    }

    return $value;
}

/**
 * получение id инфоблока по его code
 * @param $code
 * @return bool|mixed
 */
function getIblockIdCode($code)
{
    $id = false;
    $res = CIBlock::GetList(
        array(),
        array(
            'ACTIVE' => 'Y',
            "CODE" => $code
        ),
        true
    );
    if ($ar_res = $res->Fetch()) {
        $id = $ar_res['ID'];
    }
    return $id;
}

/**
 * @param $iblock_id
 * @param $element_id
 * @param array $select
 * @return bool
 */
function getElementInfo($iblock_id, $element_id, $select = array("ID", "NAME"))
{
    $result = false;
    $arFilter = array(
        "IBLOCK_ID" => IntVal($iblock_id),
        "ID" => array(0, $element_id),
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 1), $select);
    while ($ob = $res->GetNextElement()) {
        $result = $ob->GetFields();
    }
    return $result;
}

/**
 * склонение слова
 * @param $n
 * @param $form1
 * @param $form2
 * @param $form5
 * @return mixed
 */
function pluralForm($n, $form1, $form2, $form5)
{
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
        return $form5;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $form2;
    }
    if ($n1 == 1) {
        return $form1;
    }
    return $form5;
}

/**
 * Удалеие лишних символов из номера
 * @param $phone
 * @return string
 */
function normalizePhoneNumber($phone)
{
    return "+" . preg_replace("/[^0-9]/", "", $phone);
}

/**
 * id видео youtube
 * @param $link
 * @return null
 */
function getYoutubeID($link)
{
    $video_id = null;
    $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

    if (preg_match($pattern, $link, $match)) {
        $video_id = $match[1];
    };

    return $video_id;
}

/**
 * @param $link
 * @return string|null
 */
function getYoutubeEmbeddedLink($link)
{
    $id = getYoutubeID($link);

    if ($id) {
        return "https://www.youtube.com/embed/" . $id;
    }

    return null;
}

/**
 * @param $link
 * @return string|null
 */
function getYoutubeThumbnail($link)
{
    $id = getYoutubeID($link);

    if ($id) {
        return "http://i3.ytimg.com/vi/" . $id . "/maxresdefault.jpg";
    }

    return null;
}

/**
 * @param $text
 * @return string
 */
function mbUcfirst($text)
{
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

/**
 * Получение ID инфоблока по коду (или по коду и типу).
 *
 * @param string $code
 * @param string|null $type
 * @return int
 *
 * @throws RuntimeException
 */
function iblock_id($code, $type = null)
{
    return Arrilot\BitrixIblockHelper\IblockId::getByCode($code, $type);
}

/**
 * Получение данных хайлоадблока по названию его таблицы.
 * Всегда выполняет лишь один запрос в БД на скрипт и возвращает массив вида:
 *
 * array:3 [
 *   "ID" => "2"
 *   "NAME" => "Subscribers"
 *   "TABLE_NAME" => "app_subscribers"
 * ]
 *
 * @param string $table
 * @return array
 */
function highloadblock($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::getByTableName($table);
}

/**
 * Компилирование и возвращение класса для хайлоадблока для таблицы $table.
 *
 * Пример для таблицы `app_subscribers`:
 * $subscribers = highloadblock_class('app_subscribers');
 * $subscribers::getList();
 *
 * @param string $table
 * @return string
 */
function highloadblock_class($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::compileClass($table);
}

/**
 * Компилирование сущности для хайлоадблока для таблицы $table.
 * Выполняется один раз.
 *
 * Пример для таблицы `app_subscribers`:
 * $entity = \Arrilot\BitrixIblockHelper\HLblock::compileEntity('app_subscribers');
 * $query = new Entity\Query($entity);
 *
 * @param string $table
 * @return \Bitrix\Main\Entity\Base
 */
function highloadblock_entity($table)
{
    return Arrilot\BitrixIblockHelper\HLblock::compileEntity($table);
}

/**
 * Проверка на возможность редактирования заявки
 * Редирект на детальную карточку заявки, если не "Черновик"
 *
 * @param int $id
 * @param string $url
 * @param string $propertyStatusCode
 * @param string $statusDraftEnum
 * @return void
 */
function denyEditCheck(
    int $id,
    string $url,
    string $propertyStatusCode = "STATUS",
    string $statusDraftEnum = "draft"
) {
    $arSelect = ["ID", "IBLOCK_ID", "PROPERTY_" . $propertyStatusCode];
    $arFilter = [
        "ID" => $id,
    ];
    $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    $status = null;
    while ($ob = $res->GetNextElement()) {
        $arProps = $ob->GetProperties();
        $status = $arProps[$propertyStatusCode]["VALUE_XML_ID"];
    }

    if ($status != $statusDraftEnum) {
        LocalRedirect($url);
    }
}

/**
 * @param int $iblockId
 * @param array $codeProperties
 * @param string $def
 * @param string $sort
 * @return array
 * @throws \Bitrix\Main\LoaderException
 */
function getEnumListProperty(
    int $iblockId,
    array $codeProperties,
    string $def = "DESC",
    string $sort = "ASC"
) {
    \Bitrix\Main\Loader::includeModule("iblock");

    $arResult = [];
    $propertyEnums = \CIBlockPropertyEnum::GetList(
        [
            "DEF" => $def,
            "SORT" => $sort
        ],
        [
            "IBLOCK_ID" => $iblockId,
            "CODE" => $codeProperties
        ]
    );
    while ($enumFields = $propertyEnums->GetNext()) {
        $arResult[$enumFields["PROPERTY_CODE"]][$enumFields["XML_ID"]] = $enumFields;
    }

    return $arResult;
}
