<?php

/** @global $APPLICATION */
/** @var $arResult */

?>
<section class="main-page__help">
    <div class="row">
        <div class="col-12" id="header-search">
            <h2 class="help-block__title">Помощь</h2>
            <form class="help-block" autocomplete="off">
                <div class="help-block__input js-help-input"><span class="label-text">Выберите город</span>
                    <div class="help-block__autocomplete-wrapper">
                        <input
                                class="js-city-select help-block__autocomplete"
                                id="city"
                                type="text"
                                placeholder="Например Москва..."
                                data-cities="<?= implode(",", $arResult["CITIES"]); ?>"
                                spellcheck="false"
                                aria-invalid="true"
                                autocomplete="off"
                        >
                    </div>
                </div>
                <div class="help-block__input js-help-input"><span class="label-text disabled">Выберите категорию</span>
                    <div class="select js-select disabled">
                        <div class="select__selected js-category-select js-select-selected"></div>
                        <div class="select-list js-select-list">
                        </div>
                        <select class="js-select-options-list">
                        </select>
                    </div>
                </div>
                <div class="help-block__input help-block__input--art js-help-input"><span class="label-text disabled">Введите артикул или название модели</span>
                    <input class="js-art-select help-block__art js-input-art disabled" type="text"><a class="dotted-link help-block__tip-link sm" href="#">Где указан номер модели?</a>
                </div>
            </form>
            <div class="error__wrapper"></div>
            <div class="preload js-preloader">
                <div class="loading">
                    <div class="lds-css ng-scope">
                        <div class="lds-spinner" style="width:100%;height:100%">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </div>
                <p class="preload__text">Подождите еще чуть-чуть…<br>мы подбираем подходящие сервисные центры</p>
            </div>
            <div class="table-wrap on-main js-table-wrapper-main mt40">
                <div class="table-header">
                    <div class="table-cell" style="width: 27%">Название</div>
                    <div class="table-cell" style="width: 53%">Адрес</div>
                    <div class="table-cell" style="width: 20%">Телефон</div>
                </div>
                <div class="table-body js-table-body-main"></div>
            </div>
        </div>
    </div>
</section>
