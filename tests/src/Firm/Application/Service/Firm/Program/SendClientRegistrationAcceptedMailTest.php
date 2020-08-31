<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientRegistrationAcceptedMailTest extends TestBase
{
    protected $service;
    protected $clientParticipantRepository, $clientParticipant;
    protected $mailer;
    
    protected $firmId = 'firmId', $programId = 'programId', $clientId = 'clientId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->clientId)
                ->willReturn($this->clientParticipant);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendClientRegistrationAcceptedMail($this->clientParticipantRepository, $this->mailer);
    }
    
    public function test_execute_sendRegistrationAcceptedMailToClientParticipant()
    {
        $this->clientParticipant->expects($this->once())
                ->method('sendRegistrationAcceptedMail')
                ->with($this->mailer);
        $this->service->execute($this->firmId, $this->programId, $this->clientId);
    }
    
}
