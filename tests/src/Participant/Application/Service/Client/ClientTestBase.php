<?php

namespace Tests\src\Participant\Application\Service\Client;

use Participant\Application\Service\Client\ClientParticipantRepository;
use Participant\Domain\Model\ClientParticipant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ClientTestBase extends TestBase
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
    protected $firmId = 'firmId', $clientId = 'clientId', $participantId = 'participantId';
    
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
