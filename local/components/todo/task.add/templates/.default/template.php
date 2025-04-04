<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
Asset::getInstance()->addJs('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
?>

<div class="task-add-form">
    <?php if (!empty($arResult['ERRORS'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($arResult['ERRORS'] as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?= bitrix_sessid_post() ?>
        
        <div class="mb-3">
            <label for="taskName" class="form-label">Название задачи</label>
            <input type="text" class="form-control" id="taskName" name="NAME" required>
        </div>
        
        <div class="mb-3">
            <label for="taskDescription" class="form-label">Описание</label>
            <textarea class="form-control" id="taskDescription" name="DESCRIPTION" rows="3"></textarea>
        </div>
        
        <div class="mb-3">
            <label for="taskTags" class="form-label">Теги</label>
            <select class="form-control select2" id="taskTags" name="TAGS[]" multiple>
                <?php foreach ($arResult['TAGS'] as $id => $name): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="newTags" class="form-label">Новые теги (через запятую)</label>
            <input type="text" class="form-control" id="newTags" name="NEW_TAGS" placeholder="тег1, тег2, тег3">
        </div>
        
        <button type="submit" class="btn btn-primary">Создать задачу</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2').select2({
        placeholder: 'Выберите теги',
        allowClear: true
    });
});
</script> 