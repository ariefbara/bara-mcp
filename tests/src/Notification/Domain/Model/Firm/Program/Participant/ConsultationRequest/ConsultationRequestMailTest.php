<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\ {
    Model\Firm\Program\Participant\ConsultationRequest,
    SharedModel\Mail
};
use Tests\TestBase;

class ConsultationRequestMailTest extends TestBase
{

    protected $consultationRequest;
    protected $id = "newId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }

    public function test_construct_setProperties()
    {
        $consultationRequestMail = new TestableConsultationRequestMail(
                $this->consultationRequest, $this->id, "sender@email.org", "sender name", "subject", "message",
                "html message", "recipient@emai.org", "recipient name");
        
        $this->assertEquals($this->consultationRequest, $consultationRequestMail->consultationRequest);
        $this->assertEquals($this->id, $consultationRequestMail->id);
        $this->assertInstanceOf(Mail::class, $consultationRequestMail->mail);
    }

}

class TestableConsultationRequestMail extends ConsultationRequestMail
{

    public $consultationRequest;
    public $id;
    public $mail;

}
