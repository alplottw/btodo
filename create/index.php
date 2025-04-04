<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Создание задачи");
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <?$APPLICATION->IncludeComponent(
            'todo:task.add',
            '',
            []
        );?>
    </div>
</div>

<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?> 