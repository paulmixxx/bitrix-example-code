<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Localization\Loc;
use Future\Helpers\Resources;

Loc::loadMessages(__FILE__);

$asset = Resources::getInstance();
$curUrl = $APPLICATION->GetCurPage();

$asset->addCss(SITE_TEMPLATE_PATH . "/markup/dist/css/npm.swiper.*.css");
$asset->addCss(SITE_TEMPLATE_PATH . "/markup/dist/css/all.*.css");
$asset->addCss(SITE_TEMPLATE_PATH . "/markup/dist/css/fonts.*.css");

?><!DOCTYPE html>
<html lang="<?= LANGUAGE_ID; ?>">
<head>
    <? $APPLICATION->ShowHead(); ?>
    <meta name="viewport" content="initial-scale=1.0, width=device-width">
    <link rel="shortcut icon" type="image/x-icon" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" sizes="57x57" href="<?=  CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.svg');  ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.svg'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.svg'); ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.svg'); ?>">
    <link rel="apple-touch-icon" sizes="57x57" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.png'); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.png'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.png'); ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH . '/markup/dist/images/favicon-iphone.png'); ?>">
    <title><? $APPLICATION->ShowTitle(); ?></title>
</head>
<body>
<? $APPLICATION->ShowPanel(); ?>
<div class="canvas js-canvas">
    <header class="header">
        <div class="row">
            <div class="col-12">
                <div class="header__wrapper">
                    <a class="header__logo" href="<? if ($curUrl == '/'): ?>#<? else: ?>/<? endif; ?>"></a>
                    <?$APPLICATION->IncludeComponent(
						"bitrix:menu",
						"top_menu",
						array(
							"ALLOW_MULTI_SELECT" => "N",
							"CHILD_MENU_TYPE" => "left",
							"DELAY" => "N",
							"MAX_LEVEL" => "2",
							"MENU_CACHE_GET_VARS" => array(
							),
							"MENU_CACHE_TIME" => "3600000",
							"MENU_CACHE_TYPE" => "A",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"ROOT_MENU_TYPE" => "top",
							"USE_EXT" => "N",
							"COMPONENT_TEMPLATE" => "top_menu"
						),
						false
					);?>
                    <div class="header__icons">
                        <a class="header__burger" href="#">
                            <svg width="48px" height="48px" viewbox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-980.000000, -2339.000000)">
                                        <g id="icon_menu" transform="translate(980.000000, 2339.000000)">
                                            <rect class="fill" fill="#74C24A" x="16" y="17" width="15" height="2"></rect>
                                            <rect class="fill" fill="#1C7651" x="16" y="23" width="15" height="2"></rect>
                                            <rect class="fill" fill="#74C24A" x="16" y="29" width="15" height="2"></rect>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                        <a class="header__search" href="/search/">
                            <svg width="18px" height="18px" viewbox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g class="fill" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g class="fill" transform="translate(-1417.000000, -2651.000000)" fill="#212121" fill-rule="nonzero">
                                        <g id="icon_serch2" transform="translate(1417.000000, 2651.000000)">
                                            <path class="fill" id="Shape" fill="#70bc34" opacity="1" d="M7,14 C3.1,14 0,10.9 0,7 C0,3.1 3.1,0 7,0 C10.9,0 14,3.1 14,7 C14,10.9 10.9,14 7,14 Z M7,2 C4.2,2 2,4.2 2,7 C2,9.8 4.2,12 7,12 C9.8,12 12,9.8 12,7 C12,4.2 9.8,2 7,2 Z"></path>
                                            <path class="fill" opacity="0.5" d="M16.3,17.7 L13.3,14.7 C12.9,14.3 12.9,13.7 13.3,13.3 C13.7,12.9 14.3,12.9 14.7,13.3 L17.7,16.3 C18.1,16.7 18.1,17.3 17.7,17.7 C17.3,18.1 16.7,18.1 16.3,17.7 Z"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                        <a class="header__language" href="#">
                            <svg width="48px" height="48px" viewbox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-912.000000, -2339.000000)">
                                        <g id="icon_en" transform="translate(912.000000, 2339.000000)">
                                            <polygon class="fill" id="E" fill="#60BC4A" fill-rule="nonzero" points="22.51 29 16.882 29 16.882 18.892 22.398 18.892 22.398 20.446 18.996 20.446 18.996 22.938 22.132 22.938 22.132 24.492 18.996 24.492 18.996 27.418 22.51 27.418"></polygon>
                                            <polygon class="fill" id="N" fill="#006B49" fill-rule="nonzero" points="32.414 29 29.642 29 26.87 21.608 26.87 29 24.91 29 24.91 18.892 27.752 18.892 30.454 25.892 30.454 18.892 32.414 18.892"></polygon>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="main <? $APPLICATION->ShowProperty('class_for_tag_main'); ?> ">

        <? if ($USER->IsAuthorized()): ?>
            <? $APPLICATION->ShowViewContent('user_info'); ?>
        <? endif; ?>

        <? if ($curUrl == '/'): ?>
            <div class="main-page">
              <h1 class="visually-hidden"><? $APPLICATION->ShowTitle(false); ?></h1>
            </div>
        <? else: ?>
            <? if (ERROR_404 != "Y"): ?>
                <section class="top-section <? $APPLICATION->ShowProperty("CLASS_TOP_SECTION",""); ?>">
                    <div class="row">
                        <div class="col-12">
                            <h1><? $APPLICATION->ShowTitle(false); ?></h1>
                            <div class="bread-wrap">
                                <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "breadCrumbs", Array(
                                    "PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
                                        "SITE_ID" => "s1",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
                                        "START_FROM" => "0",	// Номер пункта, начиная с которого будет построена навигационная цепочка
                                    ),
                                    false
                                );?>
                            </div>
                        </div>
                    </div>
                </section>
            <? endif; ?>
        <? endif; ?>
