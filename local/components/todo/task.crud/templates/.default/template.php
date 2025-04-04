<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4"><?=$arResult['TASK'] ? 'Редактирование задачи' : 'Создание задачи'?></h5>

        <?php if (!empty($arResult['ERRORS'])): ?>
            <div class="alert alert-danger">
                <?=implode('<br>', $arResult['ERRORS'])?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?=bitrix_sessid_post()?>
            <?php if ($arResult['TASK']): ?>
                <input type="hidden" name="TASK_ID" value="<?=$arResult['TASK']['ID']?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="taskName" class="form-label">Название задачи</label>
                <input type="text" 
                       class="form-control" 
                       id="taskName" 
                       name="NAME" 
                       required
                       value="<?=htmlspecialchars($arResult['TASK']['NAME'] ?? '')?>">
            </div>

            <div class="mb-3">
                <label for="taskDescription" class="form-label">Описание</label>
                <textarea class="form-control" 
                          id="taskDescription" 
                          name="DESCRIPTION" 
                          rows="3"><?=htmlspecialchars($arResult['TASK']['DESCRIPTION'] ?? '')?></textarea>
            </div>

            <div class="mb-3">
                <label for="taskTags" class="form-label">Теги (через запятую)</label>
                <input type="text" 
                       class="form-control" 
                       id="taskTags" 
                       name="TAGS" 
                       placeholder="Например: важное, срочно, проект"
                       value="<?=htmlspecialchars($arResult['TASK']['TAGS'] ?? '')?>">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <?=$arResult['TASK'] ? 'Сохранить' : 'Создать'?>
                </button>
                <a href="/" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div> 