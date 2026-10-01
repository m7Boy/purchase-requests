<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\PurchaseRequest;

$this->title = $model->title;

$isOwner = $model->created_by == Yii::$app->user->id;
$isManager = Yii::$app->user->identity->username == 'admin';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="mb-3">

    <?= Html::a('Back', ['index'], [
        'class' => 'btn btn-secondary'
    ]) ?>

    <?php if ($model->status == PurchaseRequest::DRAFT && $isOwner): ?>

        <?= Html::a('Edit', ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary'
        ]) ?>

        <?= Html::a('Submit', ['submit', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'method' => 'post',
                'confirm' => 'Submit this request?'
            ]
        ]) ?>

    <?php endif; ?>

    <?php if ($model->status == PurchaseRequest::PENDING && $isManager && !$isOwner): ?>

        <?= Html::a('Approve', ['approve', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'method' => 'post',
                'confirm' => 'Approve this request?'
            ]
        ]) ?>

    <?php endif; ?>

</div>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title',
        'description:ntext',
        [
            'attribute' => 'amount',
            'value' => number_format($model->amount, 2)
        ],
        'status',
        [
            'attribute' => 'created_by',
            'value' => PurchaseRequest::getUserName($model->created_by)
        ],
        [
            'attribute' => 'approved_by',
            'value' => PurchaseRequest::getUserName($model->approved_by)
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime', 'php:d.m.Y H:i']
        ],
        [
            'attribute' => 'updated_at',
            'format' => ['datetime', 'php:d.m.Y H:i']
        ]
    ]
]) ?>

<?php if ($model->status == PurchaseRequest::PENDING && $isManager && !$isOwner): ?>

    <hr>

    <h3>Reject Request</h3>

    <?= Html::beginForm(['reject', 'id' => $model->id], 'post') ?>

    <div class="mb-3">
        <?= Html::textarea('comment', '', [
            'class' => 'form-control',
            'rows' => 3,
            'placeholder' => 'Reason for rejection'
        ]) ?>
    </div>

    <?= Html::submitButton('Reject', [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Reject this request?'
        ]
    ]) ?>

    <?= Html::endForm() ?>

<?php endif; ?>

<hr>

<h3>History</h3>

<?php if (!$model->logs): ?>

    <p>No history yet.</p>

<?php else: ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Old status</th>
            <th>New status</th>
            <th>Comment</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($model->logs as $log): ?>
            <tr>
                <td><?= Yii::$app->formatter->asDatetime($log->created_at, 'php:d.m.Y H:i') ?></td>
                <td><?= Html::encode(PurchaseRequest::getUserName($log->user_id)) ?></td>
                <td><?= Html::encode($log->old_status) ?></td>
                <td><?= Html::encode($log->new_status) ?></td>
                <td><?= Html::encode($log->comment) ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>