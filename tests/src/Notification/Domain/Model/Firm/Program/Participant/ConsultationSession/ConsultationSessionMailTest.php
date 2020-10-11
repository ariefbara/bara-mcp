<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\ {
    Model\Firm\Program\Participant\ConsultationSession,
    SharedModel\Mail
};
use Tests\TestBase;

class ConsultationSessionMailTest extends TestBase
{
    protected $consultationSession;
    protected $id = "newId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
    }

    public function test_construct_setProperties()
    {
        $consultationSessionMail = new TestableConsultationSessionMail(
                $this->consultationSession, $this->id, "sender@email.org", "sender name", "subject", "message",
                "html message", "recipient@emai.org", "recipient name");
        
        $this->assertEquals($this->consultationSession, $consultationSessionMail->consultationSession);
        $this->assertEquals($this->id, $consultationSessionMail->id);
        $this->assertInstanceOf(Mail::class, $consultationSessionMail->mail);
    }

}

class TestableConsultationSessionMail extends ConsultationSessionMail
{

    public $consultationSession;
    public $id;
    public $mail;

}