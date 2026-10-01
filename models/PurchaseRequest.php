<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

class PurchaseRequest extends ActiveRecord
{
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    public static function tableName()
    {
        return '{{%purchase_request}}';
    }

    public function rules()
    {
        return [
            [['title', 'amount'], 'required'],
            [['description'], 'string'],
            [['amount'], 'number', 'min' => 0.01],
            [['title'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [
                self::DRAFT,
                self::PENDING,
                self::APPROVED,
                self::REJECTED
            ]],
            [['created_by', 'approved_by', 'created_at', 'updated_at'], 'integer']
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false
            ]
        ];
    }

    public function getLogs()
    {
        return $this->hasMany(PurchaseRequestLog::class, [
            'purchase_request_id' => 'id'
        ])->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public static function getStatuses()
    {
        return [
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected'
        ];
    }

    public static function getFilterStatuses()
    {
        $statuses = self::getStatuses();

        if (Yii::$app->user->identity->username == 'admin') {
            unset($statuses[self::DRAFT]);
        }

        return $statuses;
    }

    public static function getUserName($id)
    {
        $users = [
            100 => 'admin',
            101 => 'demo'
        ];

        return $users[$id] ?? $id;
    }

    public function getStatusName()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}
