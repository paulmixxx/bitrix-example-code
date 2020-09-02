<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
$APPLICATION->SetPageProperty('class_for_tag_main', 'error-page')
?>

    <section class="section-error">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h1 class="error-title">
                        <span>404 <img class="error-img" src="<?=SITE_TEMPLATE_PATH?>/markup/dist/images/404.png" alt="404"></span></h1>
                    <p class="error-text">Что-то пошло не&nbsp;так. Вы&nbsp;можете перейти на&nbsp;главную страницу.</p>
                    <a class="button" href="/">Перейти на главную</a>
                </div>
            </div>
        </div>
    </section>

<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>