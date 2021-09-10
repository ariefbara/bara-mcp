<?php

namespace Tests\src\Query\Application\Service\Client\AsProgramParticipant;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Client\AsProgramParticipant\ClientParticipantRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Tests\TestBase;

class ClientParticipantTestBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var MockObject
     */
    protected $clientParticipant;
    protected $firmId = 'firm-id', $clientId = 'client-id', $participantId = 'participant-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('aClientParticipant')
                ->with($this->firmId, $this->clientId, $this->participantId)
                ->willReturn($this->clientParticipant);
    }

}
