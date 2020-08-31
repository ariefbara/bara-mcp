<?php

namespace Firm\Application\Listener\Firm\Program;

use Firm\Application\Service\Firm\Program\SendUserRegistrationAcceptedMail;
use Tests\TestBase;

class SendMailWhenUserRegistrationAcceptedListenerTest extends TestBase
{
    protected $listener;
    protected $sendUserRegistrationAcceptedMail;
    
    protected $event;
    protected $firmId = 'firmId', $programId = 'programId', $userId = 'userId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->sendUserRegistrationAcceptedMail = $this->buildMockOfClass(SendUserRegistrationAcceptedMail::class);
        $this->listener = new SendMailWhenUserRegistrationAcceptedListener($this->sendUserRegistrationAcceptedMail);
        
        $this->event = $this->buildMockOfInterface(UserRegistrationAcceptedEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getProgramId')->willReturn($this->programId);
        $this->event->expects($this->any())->method('getUserId')->willReturn($this->userId);
    }
    public function test_handle_executeSendUserRegistrationAcceptedMail()
    {
        $this->sendUserRegistrationAcceptedMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->programId, $this->userId);
        $this->listener->handle($this->event);
    }
}
