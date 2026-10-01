<?php

use yii\helpers\Html;

$this->title = $model->isNewRecord
    ? 'Create Purchase Request'
    : 'Update Purchase Request';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model
]) ?>