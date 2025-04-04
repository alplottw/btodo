<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

class TodoTaskAddComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('iblock')) {
            ShowError('Модуль iblock не установлен');
            return;
        }

        $this->arResult['TAGS'] = $this->getTags();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
            $this->processForm();
        }

        $this->includeComponentTemplate();
    }

    protected function getTags()
    {
        $tags = [];
        $res = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => IBLOCK_ID['TODO_TAGS'], 'ACTIVE' => 'Y'],
            false,
            false,
            ['ID', 'NAME']
        );

        while ($tag = $res->Fetch()) {
            $tags[$tag['ID']] = $tag['NAME'];
        }

        return $tags;
    }

    protected function processForm()
    {
        global $APPLICATION;
        
        $request = Application::getInstance()->getContext()->getRequest();
        
        $el = new CIBlockElement;
        
        // Получаем ID раздела на основе сессии
        $sectionId = $this->getOrCreateSection();
        
        // Подготавливаем теги
        $tagIds = [];
        $existingTags = $request->getPost('TAGS');
        $newTags = array_filter(explode(',', $request->getPost('NEW_TAGS')));
        
        // Добавляем новые теги
        foreach ($newTags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;
            
            $tagId = $this->createTag($tagName);
            if ($tagId) {
                $tagIds[] = $tagId;
            }
        }
        
        // Объединяем с существующими тегами
        if (is_array($existingTags)) {
            $tagIds = array_merge($tagIds, $existingTags);
        }
        
        $props = [
            'TAGS' => array_unique($tagIds)
        ];
        
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO'],
            'IBLOCK_SECTION_ID' => $sectionId,
            'NAME' => $request->getPost('NAME'),
            'ACTIVE' => 'Y',
            'PREVIEW_TEXT' => $request->getPost('DESCRIPTION'),
            'PROPERTY_VALUES' => $props
        ];
        
        if ($el->Add($fields)) {
            LocalRedirect($APPLICATION->GetCurPage());
        } else {
            $this->arResult['ERRORS'][] = $el->LAST_ERROR;
        }
    }
    
    protected function getOrCreateSection()
    {
        $sessionId = bitrix_sessid();
        
        // Ищем раздел по коду (sessid)
        $section = CIBlockSection::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_ID['TODO'],
                'CODE' => $sessionId
            ],
            false,
            ['ID']
        )->Fetch();
        
        if ($section) {
            return $section['ID'];
        }
        
        // Создаем новый раздел
        $bs = new CIBlockSection;
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO'],
            'NAME' => $sessionId,
            'CODE' => $sessionId,
            'ACTIVE' => 'Y'
        ];
        
        $sectionId = $bs->Add($fields);
        return $sectionId;
    }
    
    protected function createTag($name)
    {
        // Проверяем существование тега
        $res = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_ID['TODO_TAGS'],
                'NAME' => $name,
                'ACTIVE' => 'Y'
            ],
            false,
            false,
            ['ID']
        );

        if ($existingTag = $res->Fetch()) {
            return $existingTag['ID'];
        }

        // Создаем новый тег если не существует
        $el = new CIBlockElement;
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO_TAGS'],
            'NAME' => $name,
            'ACTIVE' => 'Y'
        ];
        
        return $el->Add($fields);
    }
} 