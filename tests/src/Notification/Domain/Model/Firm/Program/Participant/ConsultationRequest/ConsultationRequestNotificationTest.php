<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\Firm\Program\Participant\ConsultationRequest,
    Model\User,
    SharedModel\Notification
};
use Tests\TestBase;

class ConsultationRequestNotificationTest extends TestBase
{
    protected $consultationRequest;
    protected $notification;
    protected $consultationRequestNotification;
    protected $id = "newId", $message = "new message";
    
    protected $user;
    protected $client;
    protected $personnel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestNotification = new TestableConsultationRequestNotification($this->consultationRequest, "id", "message");
        
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->consultationRequestNotification->notification = $this->notification;
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationRequestNotification = new TestableConsultationRequestNotification($this->consultationRequest, $this->id, $this->message);
        $this->assertEquals($this->consultationRequest, $consultationRequestNotification->consultationRequest);
        $this->assertEquals($this->id, $consultationRequestNotification->id);
        $notification = new Notification($this->id, $this->message);
        $this->assertEquals($notification, $consultationRequestNotification->notification);
    }
    
    public function test_addUserRecipient_executeNotificationsAddUserRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient");
        $this->consultationRequestNotification->addUserRecipient($this->user);
    }
    public function test_addClientRecipient_executeNotificationsAddClientRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient");
        $this->consultationRequestNotification->addClientRecipient($this->client);
    }
    public function test_addPersonnelRecipient_executeNotificationsAddPersonnelRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient");
        $this->consultationRequestNotification->addPersonnelRecipient($this->personnel);
    }
}

class TestableConsultationRequestNotification extends ConsultationRequestNotification
{
    public $consultationRequest;
    public $id;
    public $notification;
}
