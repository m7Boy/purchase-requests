<?php

namespace app\services;

use Yii;
use app\models\PurchaseRequest;
use app\models\PurchaseRequestLog;

class PurchaseRequestService
{
    public function submit($model)
    {
        if ($model->status != PurchaseRequest::DRAFT) {
            throw new \Exception('Request is not in draft status.');
        }

        return $this->changeStatus($model, PurchaseRequest::PENDING);
    }

    public function approve($model)
    {
        if ($model->status != PurchaseRequest::PENDING) {
            throw new \Exception('Request is not pending.');
        }

        if ($model->created_by == Yii::$app->user->id) {
            throw new \Exception('You cannot approve your own request.');
        }

        return $this->changeStatus($model, PurchaseRequest::APPROVED);
    }

    public function reject($model, $comment)
    {
        if ($model->status != PurchaseRequest::PENDING) {
            throw new \Exception('Request is not pending.');
        }

        if (trim($comment) == '') {
            throw new \Exception('Comment is required.');
        }

        return $this->changeStatus($model, PurchaseRequest::REJECTED, $comment);
    }

    private function changeStatus($model, $status, $comment = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $oldStatus = $model->status;

            $model->status = $status;

            if ($status == PurchaseRequest::APPROVED) {
                $model->approved_by = Yii::$app->user->id;
            }

            if (!$model->save()) {
                throw new \Exception('Could not save request.');
            }

            $log = new PurchaseRequestLog();
            $log->purchase_request_id = $model->id;
            $log->user_id = Yii::$app->user->id;
            $log->old_status = $oldStatus;
            $log->new_status = $status;
            $log->comment = $comment;
            $log->created_at = time();

            if (!$log->save()) {
                throw new \Exception('Could not save request log.');
            }

            $transaction->commit();

            try {
                $this->sendNotification($model, $status);
            } catch (\Throwable $e) {
                Yii::error($e->getMessage());
            }

            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function sendNotification($model, $status)
    {
        $email = $this->getUserEmail($model->created_by);

        if (!$email) {
            return;
        }

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setSubject('Purchase request status changed')
            ->setTextBody(
                'Your request "' . $model->title . '" is now ' . $status . '.'
            )
            ->send();
    }

    private function getUserEmail($userId)
    {
        $users = [
            100 => null,
            101 => null
        ];

        return $users[$userId] ?? null;
    }
}