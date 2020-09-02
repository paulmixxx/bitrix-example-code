<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Localization\Loc;
use Future\Helpers\Resources;

Loc::loadMessages(__FILE__);

$asset = Resources::getInstance();
$curUrl = $APPLICATION->GetCurPage();

?>
    <aside class="side-menu js-side">side-menu</aside>
    <div class="popup-bg js-popup-bg">
        <div class="popup" id="popup">
            <div class="popup__close js-popup-close">
                <svg width="24px" height="24px" viewbox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1540.000000, -2353.000000)" fill="#212121" fill-rule="nonzero">
                            <g transform="translate(1540.000000, 2353.000000)">
                                <path class="fill-close" d="M12,0 C18.636,0 24,5.364 24,12 C24,18.636 18.636,24 12,24 C5.364,24 0,18.636 0,12 C0,5.364 5.364,0 12,0 Z M8.04,8.04 C7.572,8.508 7.572,9.264 8.04,9.732 L10.308,12 L8.04,14.268 C7.572,14.736 7.572,15.492 8.04,15.96 C8.508,16.428 9.264,16.428 9.732,15.96 L12,13.692 L14.268,15.96 C14.736,16.428 15.492,16.428 15.96,15.96 C16.428,15.492 16.428,14.736 15.96,14.268 L13.692,12 L15.96,9.732 C16.428,9.264 16.428,8.508 15.96,8.04 C15.492,7.584 14.724,7.584 14.268,8.04 L12,10.308 L9.732,8.04 C9.264,7.572 8.508,7.572 8.04,8.04 Z"></path>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="popup__loaded js-popup-loaded"></div>
        </div>
    </div>
    <script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/runtime.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.core-js.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.moment.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.jquery.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.popperjs.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.swiper.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.rx.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/npm.tippy.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/vendors~all.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/all.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/fonts.*.js'); ?>"></script>
	<script src="<?= $asset->getPath(SITE_TEMPLATE_PATH.'/markup/dist/js/images.*.js'); ?>"></script>
</body>
</html>
