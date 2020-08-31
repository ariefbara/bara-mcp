<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\UserParticipant;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendUserRegistrationAcceptedMailTest extends TestBase
{
    protected $service;
    protected $userParticipantRepository, $userParticipant;
    protected $mailer;
    
    protected $firmId = 'firmid', $programId = 'programId', $userId = 'userId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->userId)
                ->willReturn($this->userParticipant);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendUserRegistrationAcceptedMail($this->userParticipantRepository, $this->mailer);
    }
    public function test_execute_sendRegistrationAcceptedMailOnUserParticipant()
    {
        $this->userParticipant->expects($this->once())
                ->method('sendRegistrationAcceptedMail')
                ->with($this->mailer);
        $this->service->execute($this->firmId, $this->programId, $this->userId);
    }
}
