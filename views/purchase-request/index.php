<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\PurchaseRequest;

$this->title = 'Purchase Requests';

$isManager = Yii::$app->user->identity->username == 'admin';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!$isManager): ?>
        <?= Html::a('Create Request', ['create'], [
            'class' => 'btn btn-primary'
        ]) ?>
    <?php endif ?>
</div>

<div class="mb-3">
    <?= Html::beginForm(['index'], 'get') ?>

    <?= Html::dropDownList('status', $status, PurchaseRequest::getFilterStatuses(), [
        'prompt' => 'All statuses',
        'class' => 'form-select',
        'style' => 'max-width: 250px; display: inline-block'
    ]) ?>

    <?= Html::submitButton('Filter', [
        'class' => 'btn btn-secondary'
    ]) ?>

    <?= Html::a('Clear', ['index'], [
        'class' => 'btn btn-link'
    ]) ?>

    <?= Html::endForm() ?>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        [
            'attribute' => 'amount',
            'value' => function ($model) {
                return number_format($model->amount, 2);
            }
        ],
        'status',
        [
            'attribute' => 'created_at',
            'format' => ['datetime', 'php:d.m.Y H:i']
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'visibleButtons' => [
                'update' => function ($model) {
                    return $model->status == PurchaseRequest::DRAFT
                        && $model->created_by == Yii::$app->user->id;
                }
            ]
        ]
    ]
]) ?>