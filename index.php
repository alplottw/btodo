<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
?>

<?php

global $arFilter;
$arFilter = array(
    "SECTION_CODE" => bitrix_sessid()
);

// Добавляем поиск по названию, если есть поисковый запрос
if (!empty($_GET['search'])) {
    $arFilter["%NAME"] = $_GET['search'];
}

$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "todo_list",
    Array(
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "content",
        "IBLOCK_ID" => IBLOCK_ID['TODO'],
        "NEWS_COUNT" => "3",
        "SORT_BY1" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_BY2" => "ID",
        "SORT_ORDER2" => "DESC",
        "FILTER_NAME" => "arFilter",
        "FIELD_CODE" => Array("ID", "NAME", "PREVIEW_TEXT", "DATE_CREATE"),
        "PROPERTY_CODE" => Array("TAGS"),
        "CHECK_DATES" => "N",
        "DETAIL_URL" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "ACTIVE_DATE_FORMAT" => "d.m.Y H:i",
        "SET_TITLE" => "N",
        "SET_STATUS_404" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "3600",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Задачи",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "bootstrap_v5",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => ""
    )
);?>

<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>