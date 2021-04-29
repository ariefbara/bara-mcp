<?php

namespace Tests\src\Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Client\ProgramParticipant\ClientParticipantRepository;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Tests\TestBase;

class ProgramParticipantTestBase extends TestBase
{
    protected $clientParticipantRepository;
    protected $clientParticipant;
    protected $firmId = 'firm-id', $clientId = 'client-id', $programId = 'program-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipantCorrespondWithProgram')
                ->with($this->firmId, $this->clientId, $this->programId)
                ->willReturn($this->clientParticipant);
    }
}
