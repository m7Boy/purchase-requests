<?php

namespace app\models;

use yii\db\ActiveRecord;

class PurchaseRequestLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%purchase_request_log}}';
    }

    public function rules()
    {
        return [
            [['purchase_request_id', 'user_id', 'new_status', 'created_at'], 'required'],
            [['purchase_request_id', 'user_id', 'created_at'], 'integer'],
            [['old_status', 'new_status'], 'string', 'max' => 20],
            [['comment'], 'string']
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(PurchaseRequest::class, [
            'id' => 'purchase_request_id'
        ]);
    }
}
