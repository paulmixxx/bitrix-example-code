<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var $arParams */
/** @var $arResult */

?>
<div class="personal-infocard">
    <div class="row">
        <div class="col-12">
            <div class="personal-infocard__wrapper js-with-shadows">
                <div class="personal-infocard__list drag-scroll js-drag-scroll">
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">Логин</div>
                        <div class="infocard-item__value">
                            <?php if ($arResult["LOGIN"]) : ?>
                                <?= $arResult["LOGIN"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">ФИО контакта</div>
                        <div class="infocard-item__value">
                            <?php if ($arResult["FULL_NAME_FORMAT"]) : ?>
                                <?= $arResult["FULL_NAME_FORMAT"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">Наименование СЦ</div>
                        <div class="infocard-item__value">
                            <?php if ($arResult["SERVICE_NAME"]) : ?>
                                <?= $arResult["SERVICE_NAME"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">Номер договора</div>
                        <div class="infocard-item__value">
                            <?php if ($arResult["CONTRACT_NUMBER"]) : ?>
                                <?= $arResult["CONTRACT_NUMBER"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">Телефон</div>
                        <div class="infocard-item__value">
                            <?php if ($arResult["PHONE"]) : ?>
                                <?= $arResult["PHONE"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="infocard-item">
                        <div class="subtitle-small infocard-item__title">Адрес доставки</div>
                        <div class="infocard-item__value" <?php if ($arResult["ADDRESS_FORMAT"]) :
                            ?>title="<?= $arResult["ADDRESS_FORMAT"]; ?>"<?php
                                                          endif; ?>>
                            <?php if ($arResult["ADDRESS_FORMAT"]) : ?>
                                <?= $arResult["ADDRESS_FORMAT"] ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
