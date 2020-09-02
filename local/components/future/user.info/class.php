<?php

use Future\Models\UserInfo;

class CUserInfo extends CBitrixComponent
{
    /**
     * @var CUser
     */
    private $user;

    public function onPrepareComponentParams($arParams)
    {
        $this->user = $GLOBALS["USER"];

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->StartResultCache(false, $this->user->GetID())) {
            $user = new UserInfo($this->user);

            $this->arResult["LOGIN"] = $user->getLogin();
            $this->arResult["FULL_NAME"] = $user->getFullNameRaw();
            $this->arResult["FULL_NAME_FORMAT"] = $user->getFullName();
            $this->arResult["SERVICE_NAME"] = $user->getServiceName();
            $this->arResult["CONTRACT_NUMBER"] = $user->getContractNumber();
            $this->arResult["PHONE"] = $user->getPhone();
            $this->arResult["ADDRESS"] = $user->getAddressRaw();
            $this->arResult["ADDRESS_FORMAT"] = $user->getAddress();

            $this->includeComponentTemplate();
        }

        return $this->arResult;
    }
}
