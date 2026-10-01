<?php

namespace tests\unit;

use Yii;
use app\models\PurchaseRequest;
use app\models\PurchaseRequestLog;
use app\services\PurchaseRequestService;

class PurchaseRequestWorkflowTest extends \Codeception\Test\Unit
{
    public function testSubmitRequest()
    {
        Yii::$app->user->login(\app\models\User::findIdentity(101));

        $request = new PurchaseRequest();
        $request->title = 'Test request';
        $request->description = 'Test description';
        $request->amount = 500;
        $request->status = PurchaseRequest::DRAFT;

        $this->assertTrue($request->save());

        $service = new PurchaseRequestService();
        $service->submit($request);

        $request->refresh();

        $this->assertEquals(
            PurchaseRequest::PENDING,
            $request->status
        );

        $log = PurchaseRequestLog::find()
            ->where(['purchase_request_id' => $request->id])
            ->one();

        $this->assertNotNull($log);
        $this->assertEquals(PurchaseRequest::DRAFT, $log->old_status);
        $this->assertEquals(PurchaseRequest::PENDING, $log->new_status);

        $request->delete();
    }
}