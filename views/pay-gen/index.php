<?php
/**
 * Вид для отслеживания генерации запросов
 *
 * @var $data array \app\controllers\PayGenController
 * @var $resp mixed \app\controllers\PayGenController
 */

$this->title = 'Генератор запросов';

$script = <<< JS
$(document).ready(function() {
    setInterval(function(){ location.reload(); }, 20000);
});
JS;
$this->registerJs($script);
?>

<p class="container">

    <p><a class="btn btn-lg btn-success" href="<?= Yii::$app->homeUrl ?>">Остановить генератор</a></p>

    <!-- Ответ с первого сервиса -->
    <?php if (\Yii::$app->session->hasFlash('writed')): ?>
        <p class="text-success"><span class="glyphicon glyphicon-ok"></span>
            <?= \Yii::$app->session->getFlash('writed') ?>
        </p>
    <?php endif; ?>
    <?php if (\Yii::$app->session->hasFlash('notWrited')): ?>
        <p class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span>
            <?= \Yii::$app->session->getFlash('notWrited') ?>
        </p>
    <?php endif; ?>
    <!-- /Ответ с первого сервиса -->

    <h2>Список запросов:</h2>

    <?php foreach ($data as $key => $value): ?>
    <b>ID Запроса: </b><?= $key ?><br/>
    <b>Сумма: </b><?= $value['sum'] ?><br/>
    <b>Комиссия: </b><?= $value['commission'] ?><br/>
    <b>ID клиента: </b><?= $value['user_id'] ?><br/>
    <hr/>
    <?php endforeach; ?>

    <div class="clearfix"></div>

    <blockquote>
        <?= $resp; ?>
    </blockquote>
</div>