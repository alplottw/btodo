<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => "Управление задачами",
    "DESCRIPTION" => "Компонент для добавления и удаления задач",
    "ICON" => "/images/icon.gif",
    "SORT" => 20,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "todo",
        "NAME" => "TODO"
    ),
); 