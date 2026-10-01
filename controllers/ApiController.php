<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\PurchaseRequest;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionPending()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $requests = PurchaseRequest::find()
            ->where(['status' => PurchaseRequest::PENDING])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $data = [];

        foreach ($requests as $request) {
            $data[] = [
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => $request->status,
                'created_by' => PurchaseRequest::getUserName($request->created_by),
                'created_at' => date('Y-m-d H:i:s', $request->created_at)
            ];
        }

        return $data;
    }
}