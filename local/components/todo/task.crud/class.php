<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

class TodoTaskCrudComponent extends CBitrixComponent
{

    public function deleteAction($id)
    {
        if (!$id) {
            return ['success' => false, 'error' => 'ID не указан'];
        }

        $el = new CIBlockElement;
        
        // Проверяем, что элемент принадлежит текущей сессии
        $item = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_ID['TODO'],
                'ID' => $id,
                'SECTION_CODE' => bitrix_sessid()
            ],
            false,
            false,
            ['ID']
        )->Fetch();

        if (!$item) {
            return ['success' => false, 'error' => 'Задача не найдена или нет прав на удаление'];
        }

        if ($el->Delete($id)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Ошибка удаления'];
        }
    }

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

        $request = Application::getInstance()->getContext()->getRequest();
        
        if ($request->get('action') === 'delete' && $request->get('id') && check_bitrix_sessid()) {
            $this->deleteTask($request->get('id'));
            LocalRedirect('/');
            return;
        }

        // Загружаем данные задачи для редактирования
        $taskId = $request->get('id');
        if ($taskId) {
            $this->loadTask($taskId);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
            $this->processForm();
        }

        $this->includeComponentTemplate();
    }

    protected function loadTask($id)
    {
        $item = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_ID['TODO'],
                'ID' => $id,
                'SECTION_CODE' => bitrix_sessid()
            ],
            false,
            false,
            ['ID', 'NAME', 'PREVIEW_TEXT', 'PROPERTY_TAGS']
        )->GetNext();

        if ($item) {
            // Получаем теги
            $tagNames = [];
            if (!empty($item['PROPERTY_TAGS_VALUE'])) {
                $res = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => IBLOCK_ID['TODO_TAGS'],
                        'ID' => $item['PROPERTY_TAGS_VALUE']
                    ],
                    false,
                    false,
                    ['ID', 'NAME']
                );
                while ($tag = $res->Fetch()) {
                    $tagNames[] = $tag['NAME'];
                }
            }

            $this->arResult['TASK'] = [
                'ID' => $item['ID'],
                'NAME' => $item['NAME'],
                'DESCRIPTION' => $item['PREVIEW_TEXT'],
                'TAGS' => implode(', ', $tagNames)
            ];
        }
    }

    protected function processForm()
    {
        global $APPLICATION;
        
        $request = Application::getInstance()->getContext()->getRequest();
        $taskId = $request->getPost('TASK_ID');
        
        $el = new CIBlockElement;
        
        // Получаем ID раздела на основе сессии
        $sectionId = $this->getOrCreateSection();
        
        // Обрабатываем теги
        $tagIds = [];
        $tags = array_filter(array_map('trim', explode(',', $request->getPost('TAGS'))));
        
        foreach ($tags as $tagName) {
            if (empty($tagName)) continue;
            $tagId = $this->createTag($tagName);
            if ($tagId) {
                $tagIds[] = $tagId;
            }
        }
        
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO'],
            'IBLOCK_SECTION_ID' => $sectionId,
            'NAME' => $request->getPost('NAME'),
            'ACTIVE' => 'Y',
            'PREVIEW_TEXT' => $request->getPost('DESCRIPTION'),
            'PROPERTY_VALUES' => [
                'TAGS' => array_unique($tagIds)
            ]
        ];
        
        if ($taskId) {
            // Проверяем права на редактирование
            $item = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => IBLOCK_ID['TODO'],
                    'ID' => $taskId,
                    'SECTION_CODE' => bitrix_sessid()
                ],
                false,
                false,
                ['ID']
            )->Fetch();

            if ($item && $el->Update($taskId, $fields)) {
                LocalRedirect('/');
            } else {
                $this->arResult['ERRORS'][] = $el->LAST_ERROR;
            }
        } else {
            if ($el->Add($fields)) {
                LocalRedirect('/');
            } else {
                $this->arResult['ERRORS'][] = $el->LAST_ERROR;
            }
        }
    }
    
    protected function getOrCreateSection()
    {
        $sessionId = bitrix_sessid();
        
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
        
        $bs = new CIBlockSection;
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO'],
            'NAME' => $sessionId,
            'CODE' => $sessionId,
            'ACTIVE' => 'Y'
        ];
        
        return $bs->Add($fields);
    }
    
    protected function createTag($name)
    {
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

        $el = new CIBlockElement;
        $fields = [
            'IBLOCK_ID' => IBLOCK_ID['TODO_TAGS'],
            'NAME' => $name,
            'ACTIVE' => 'Y'
        ];
        
        return $el->Add($fields);
    }

    protected function deleteTask($id)
    {
        if (!$id) {
            return;
        }

        $el = new CIBlockElement;
        
        $item = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_ID['TODO'],
                'ID' => $id,
                'SECTION_CODE' => bitrix_sessid()
            ],
            false,
            false,
            ['ID']
        )->Fetch();

        if (!$item) {
            return;
        }

        $el->Delete($id);
    }
} 