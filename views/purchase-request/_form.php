<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();
?>

<?= $form->field($model, 'title', ['options' => ['class' => 'pb-3']])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description', ['options' => ['class' => 'pb-3']])->textarea(['rows' => 5]) ?>

<?= $form->field($model, 'amount', ['options' => ['class' => 'pb-3']])->textInput(['type' => 'number', 'step' => '0.01']) ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    
    <?= Html::a('Back', ['index'], [
        'class' => 'btn btn-secondary'
    ]) ?>
</div>

<?php ActiveForm::end(); ?>