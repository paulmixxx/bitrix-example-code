<?php

namespace Future\Events\Iblock;

class RepairListField
{
    public function getUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "custom-repair-list",
            "DESCRIPTION" => 'Future | Запчасти',
            "GetPublicViewHTML" => [__CLASS__, "getPublicViewHTML"],
            "GetAdminListViewHTML" => [__CLASS__, "getAdminListViewHTML"],
            "GetPropertyFieldHtml" => [__CLASS__, "getPropertyFieldHtml"],
            "ConvertToDB" => [__CLASS__, "convertToDB"],
            "ConvertFromDB" => [__CLASS__, "convertFromDB"],
        );
    }

    // Отображение в публичной части
    public function getPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        /*return self::ShowTable(self::BuildTable($value['VALUE']));*/
    }

    // Показ в списке
    public function getAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {

        if (strlen($value["VALUE"]) > 0) {
            return str_replace(" ", "&nbsp;", htmlspecialcharsex($value["VALUE"]));
        } else {
            return '&nbsp;';
        }
    }

    public function getSettingsHTML()
    {
        return '';
    }

    // отображение формы редактирования в админке и в режиме правки
    public function getPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $return = "
           <table>
               <thead>
                   <tr>
                        <th>Артикул</th>
                        <th>Название</th>
                        <th>Количество</th>
                   </tr>
               </thead>
               <tbody>
        ";
        if ($value["VALUE"]) {
            $valueVendorCode = $value["VALUE"]["VENDOR_CODE"];
            $valueName = $value["VALUE"]["NAME"];
            $valueCount = $value["VALUE"]["COUNT"];
            $return .= "
            <tr>
                <td><input size='15' type='text' name='$strHTMLControlName[VALUE][VENDOR_CODE]' value='$valueVendorCode' /></td>
                <td><input size='30' type='text' name='$strHTMLControlName[VALUE][NAME]' value='$valueName'/></td>
                <td><input size='10' type='text' name='$strHTMLControlName[VALUE][COUNT]' value='$valueCount'/></td>
            </tr>
            ";
        } else {
            $return .= "
            <tr>
                <td><input size='15' type='text' name='$strHTMLControlName[VALUE][VENDOR_CODE]' /></td>
                <td><input size='30' type='text' name='$strHTMLControlName[VALUE][NAME]' /></td>
                <td><input size='10' type='text' name='$strHTMLControlName[VALUE][COUNT]' /></td>
            </tr>
            ";
        }
        $return .= "</tbody></table>";

        return $return;
    }

    //Сохранение в БД
    public function convertToDB($arProperty, $value)
    {
        $return = false;
        if (is_array($value) && strlen(trim($value["VALUE"]["VENDOR_CODE"])) && strlen(trim($value["VALUE"]["NAME"])) && ((int) $value["VALUE"]["COUNT"] >= 1)) {
            $return = array("VALUE" => serialize($value["VALUE"]));

            if (strlen(trim($value["DESCRIPTION"])) > 0) {
                $return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
            }
        }
        return $return;
    }

    //Извлечение из БД
    public function convertFromDB($arProperty, $value)
    {
        $return = false;
        if (!is_array($value["VALUE"])) {
            $return = array("VALUE" => unserialize($value["VALUE"]));
            if ($value["DESCRIPTION"]) {
                $return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
            }
        }
        return $return;
    }
}
