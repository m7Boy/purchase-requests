<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'My Yii Application';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

    <!-- Hero banner with Yii gradient -->
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        
        <div class="position-relative">
            <h1 class="display-5 fw-bold mb-3">Purchase Requests - Yii Framework</h1>
            
            <div class="d-flex gap-2 flex-wrap">
                <?= Html::a(
                    'Create request',
                    '/purchase-request/create',
                    [
                        'class' => 'btn btn-light btn-lg fw-semibold px-4',
                        'rel' => 'noopener',
                    ],
                ) ?>
            </div>
        </div>
    </div>
</div>
