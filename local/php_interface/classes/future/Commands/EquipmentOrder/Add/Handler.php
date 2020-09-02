<?php

namespace Future\Commands\EquipmentOrder\Add;

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\LoaderException;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\PropertyValue;
use CIBlockElement;
use CUser;
use DomainException;
use Exception;
use Future\Models\UserInfo;

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

    /**
     * Handler constructor.
     * @param CUser $user
     * @param CIBlockElement $CIBlockElement
     * @throws LoaderException
     */
    public function __construct(CUser $user)
    {
        Loader::includeModule("iblock");
        Loader::includeModule("sale");
        Loader::includeModule("catalog");

        $this->user = $user;
        $this->CIBlockElement = new CIBlockElement();
    }

    /**
     * @param Command $command
     * @return int
     * @throws Exception
     */
    public function handle(Command $command)
    {
        if (!$this->user->IsAuthorized()) {
            throw new Exception("Unauthorized.", 401);
        }

        if (empty($command->ELEMENT_ID)) {
            throw new Exception("Id is empty.", 400);
        }

        $requestElement = null;
        $res = CIBlockElement::GetByID($command->ELEMENT_ID);

        if ($resObj = $res->GetNextElement()) {
            $requestElement = $resObj->GetFields();
            $requestElement["PROPERTIES"] = $resObj->GetProperties();
        }

        if (!$requestElement || $requestElement["PROPERTIES"]["USER"]["VALUE"] != $this->user->GetID()) {
            throw new Exception("Not Found.", 404);
        }

        $orderId = (int) $requestElement["PROPERTIES"]["ORDER"]["VALUE"];

        $order = null;
        if ($orderId > 0) {
            $order = Order::load($orderId);
        }

        if ($order) {
            throw new DomainException("Заказ уже создан.");
        }

        $vendorCodeList = array_column($requestElement["PROPERTIES"]["EQUIPMENT"]["VALUE"], "VENDOR_CODE");
        $sparePartsList = [];
        $arSelect = [];
        $arFilter = [
            "IBLOCK_ID" => iblock_id("catalog:equipment"),
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "PROPERTY_VENDOR_CODE" => $vendorCodeList
        ];
        $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $sparePartsList[$arProps["VENDOR_CODE"]["VALUE"]] = $arFields;
            $sparePartsList[$arProps["VENDOR_CODE"]["VALUE"]]["PROPERTIES"] = $arProps;
        }

        foreach ($vendorCodeList as $vendorCode) {
            if (!array_key_exists($vendorCode, $sparePartsList)) {
                throw new Exception("Оборудования с артикулом '$vendorCode' не существует.", 404);
            }
        }

        $siteId = Context::getCurrent()->getSite();
        $currency = CurrencyManager::getBaseCurrency();
        $basket = Basket::create($siteId);

        foreach ($requestElement["PROPERTIES"]["EQUIPMENT"]["VALUE"] as $item) {
            $productItem = $sparePartsList[$item["VENDOR_CODE"]];
            $productItemId = (int) $productItem["ID"];
            $count = (int) $item["COUNT"];

            if ($basketItem = $basket->getExistsItem('catalog', $productItemId)) {
                $basketItem->setField(
                    'QUANTITY',
                    $basketItem->getQuantity() + $count
                );
            } else {
                $basketItem = $basket->createItem('catalog', $productItemId);
                $basketItem->setFields(
                    [
                         'QUANTITY' => $count,
                         'CURRENCY' => $currency,
                         'LID' => $siteId,
                         'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    ]
                );

                $collection = $basketItem->getPropertyCollection();

                $itemBasketCollection = $collection->createItem();
                $itemBasketCollection->setFields([
                    'NAME' => 'Артикул',
                    'CODE' => 'VENDOR_CODE',
                    'VALUE' => $item["VENDOR_CODE"],
                ]);
            }
        }

        $basket->save();

        $order = Order::create($siteId, $this->user->GetID());
        $order->setBasket($basket);
        $order->setPersonTypeId(1);

        $userInfo = new UserInfo($this->user);

        $propertyCollection = $order->getPropertyCollection();

        foreach ($propertyCollection as $property) {
            /** @var PropertyValue $property */
            $propertyCode = $property->getField("CODE");

            switch ($propertyCode) {
                case "INN":
                    $property->setValue($userInfo->getInn());
                    break;
                case "KPP":
                    $property->setValue($userInfo->getKpp());
                    break;
                default:
                    $property->setValue($requestElement["PROPERTIES"][$propertyCode]["VALUE"]);
            }
        }

        $rsUser = CUser::GetByID($this->user->GetID());
        $arUser = $rsUser->Fetch();

        $propertyCollection->getPhone()->setValue($requestElement["PROPERTIES"]["PHONE"]["VALUE"]);
        $propertyCollection->getPayerName()->setValue($requestElement["PROPERTIES"]["NAME"]["VALUE"]);
        $propertyCollection->getUserEmail()->setValue($this->user->GetEmail());

        $result = $order->save();

        if ($result->isSuccess()) {
            $orderId = $result->getId();
            $this->CIBlockElement->SetPropertyValuesEx(
                $command->ELEMENT_ID,
                false,
                [
                    "ORDER" => ["VALUE" => $orderId]
                ]
            );

            return $orderId;
        }

        throw new Exception($result->getErrors(), 400);
    }
}
