<?php

namespace Future\Models;

use CUser;

class UserInfo
{
    /**
     * @var CUser
     */
    private $user;
    private $login;
    private $firstName;
    private $lastName;
    private $middleName;
    private $serviceName;
    private $contractNumber;
    private $phone;
    private $country;
    private $state;
    private $city;
    private $street;
    private $postZip;
    private $inn;
    private $kpp;

    public function __construct(CUser $user)
    {
        $this->user = $user;
        $this->init();
    }

    private function init()
    {
        $by = "ID";
        $order = "ASC";
        $rsUser = CUser::GetList(
            $by,
            $order,
            [
                "ID" => $this->user->GetID()
            ],
            [
                "FIELDS" => [
                    "LOGIN",
                    "NAME",
                    "SECOND_NAME",
                    "LAST_NAME",
                    "PERSONAL_PHONE",
                    "PERSONAL_COUNTRY",
                    "PERSONAL_STATE",
                    "PERSONAL_CITY",
                    "PERSONAL_STREET",
                    "PERSONAL_ZIP",
                ],
                "SELECT" => [
                    "UF_SERVICE_CENTER_NAME",
                    "UF_CONTRACT_NUMBER",
                    "UF_OOO_INN",
                    "UF_OOO_KPP",
                ]
            ]
        );
        if ($arUser = $rsUser->Fetch()) {
            $this->login = $arUser["LOGIN"];
            $this->firstName = $arUser["NAME"];
            $this->lastName = $arUser["LAST_NAME"];
            $this->middleName = $arUser["SECOND_NAME"];
            $this->serviceName = $arUser["UF_SERVICE_CENTER_NAME"];
            $this->contractNumber = $arUser["UF_CONTRACT_NUMBER"];
            $this->phone = $arUser["PERSONAL_PHONE"];
            $this->country = GetCountryByID($arUser["PERSONAL_COUNTRY"], "ru");
            $this->state = $arUser["PERSONAL_STATE"];
            $this->city = $arUser["PERSONAL_CITY"];
            $this->street = $arUser["PERSONAL_STREET"];
            $this->postZip = $arUser["PERSONAL_ZIP"];
            $this->inn = $arUser["UF_OOO_INN"];
            $this->kpp = $arUser["UF_OOO_KPP"];
        }
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getFullName($format = 'short')
    {
        switch ($format) {
            case 'full':
                return implode(
                    " ",
                    array_filter([
                        $this->lastName,
                        $this->firstName,
                        $this->middleName
                    ])
                );
            default:
                return implode(
                    " ",
                    array_filter([
                        $this->lastName,
                        $this->getInitialsChar($this->middleName),
                        $this->getInitialsChar($this->firstName)
                    ])
                );
        }
    }

    public function getFullNameRaw()
    {
        return [
            "FIRST_NAME" => $this->firstName,
            "LAST_NAME" => $this->lastName,
            "MIDDLE_NAME" => $this->middleName,
        ];
    }

    private function getInitialsChar($name)
    {
        if ($name) {
            return mb_substr($name, 0, 1) . '.';
        }

        return false;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function getContractNumber()
    {
        return $this->contractNumber;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getAddress()
    {
        return implode(
            ", ",
            array_filter([
                $this->country,
                $this->state,
                $this->city,
                $this->street,
                $this->postZip ? 'индекс ' . $this->postZip : $this->postZip,
            ])
        );
    }

    public function getAddressRaw()
    {
        return [
            "COUNTRY" => $this->country,
            "STATE" => $this->state,
            "CITY" => $this->city,
            "STREET" => $this->street,
            "POST_ZIP" => $this->postZip,
        ];
    }

    public function getInn()
    {
        return $this->inn;
    }

    public function getKpp()
    {
        return $this->kpp;
    }
}
