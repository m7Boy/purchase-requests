<?php

namespace app\controllers;

use Yii;
use app\models\PurchaseRequest;
use app\services\PurchaseRequestService;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class PurchaseRequestController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'submit' => ['post'],
                    'approve' => ['post'],
                    'reject' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = PurchaseRequest::find();

        if (Yii::$app->user->identity->username == 'admin') {
            $query->andWhere(['!=', 'status', PurchaseRequest::DRAFT]);
        }

        $status = Yii::$app->request->get('status');

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionCreate()
    {
        $model = new PurchaseRequest();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('PurchaseRequest', []);

            $model->title = $data['title'] ?? null;
            $model->description = $data['description'] ?? null;
            $model->amount = $data['amount'] ?? null;
            $model->status = PurchaseRequest::DRAFT;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Request created.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->status != PurchaseRequest::DRAFT) {
            throw new \Exception('Only draft requests can be edited.');
        }

        if ($model->created_by != Yii::$app->user->id) {
            throw new \Exception('You cannot edit this request.');
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('PurchaseRequest', []);

            $model->title = $data['title'] ?? null;
            $model->description = $data['description'] ?? null;
            $model->amount = $data['amount'] ?? null;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Request updated.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    public function actionSubmit($id)
    {
        $model = $this->findModel($id);

        if ($model->created_by != Yii::$app->user->id) {
            throw new \Exception('You cannot submit this request.');
        }

        try {
            (new PurchaseRequestService())->submit($model);
            Yii::$app->session->setFlash('success', 'Request submitted.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionApprove($id)
    {
        $this->checkManager();

        $model = $this->findModel($id);

        try {
            (new PurchaseRequestService())->approve($model);
            Yii::$app->session->setFlash('success', 'Request approved.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReject($id)
    {
        $this->checkManager();

        $model = $this->findModel($id);
        $comment = Yii::$app->request->post('comment', '');

        try {
            (new PurchaseRequestService())->reject($model, $comment);
            Yii::$app->session->setFlash('success', 'Request rejected.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    protected function findModel($id)
    {
        $model = PurchaseRequest::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Request not found.');
        }

        return $model;
    }

    protected function checkManager()
    {
        if (Yii::$app->user->identity->username != 'admin') {
            throw new \Exception('Manager access required.');
        }
    }
}