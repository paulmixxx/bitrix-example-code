<?php

namespace Future\Commands\WarrantyRepairPhoto\Delete;

use Bitrix\Main\Loader;
use CUser;
use CIBlockElement;
use Exception;

class Handler
{
    /**
     * @var CUser
     */
    private $user;
    /**
     * @var CIBlockElement
     */
    private $CIBlockElement;

    public function __construct(CUser $user)
    {
        Loader::includeModule("iblock");

        $this->user = $user;
        $this->CIBlockElement = new CIBlockElement();
    }

    /**
     * @param Command $command
     * @return bool|int
     * @throws Exception
     */
    public function handle(Command $command)
    {
        if (!$this->user->IsAuthorized()) {
            throw new Exception("Unauthorized", 401);
        }

        if (empty($command->ID)) {
            throw new Exception("Id is empty", 400);
        }

        $element = null;
        $res = CIBlockElement::GetByID($command->ID);

        if ($resObj = $res->GetNextElement()) {
            $element = $resObj->GetFields();
            $element["PROPERTIES"] = $resObj->GetProperties();
        }

        if (!$element || $element["PROPERTIES"]["USER"]["VALUE"] != $this->user->GetID()) {
            throw new Exception("Not Found", 404);
        }

        $allowedProperties = [
            "PHOTO_SCAN_RECEIPT",
            "PHOTO_NAMEPLATE",
            "PHOTO_EQUIPMENT",
            "PHOTO_NAMEPLATE_BEFORE_DISPOSAL",
            "PHOTO_NAMEPLATE_AFTER_DISPOSAL"
        ];
        if (!in_array($command->CODE, $allowedProperties)) {
            throw new Exception("Forbidden", 403);
        }

        $this->CIBlockElement->SetPropertyValuesEx($command->ID, false, array($command->CODE => ['del' => 'Y']));

        return true;
    }
}
