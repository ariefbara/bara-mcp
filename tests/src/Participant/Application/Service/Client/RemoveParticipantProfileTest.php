<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ParticipantProfileRepository;
use Participant\Domain\Model\ClientParticipant;
use Tests\TestBase;

class RemoveParticipantProfileTest extends TestBase
{
    protected $clientParticipantRepository, $clientParticipant;
    protected $participantProfileRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $programParticipationId = "clientParticipantId",
            $programsProfileFormId = "programProfileFormId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method("aClientParticipant")
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);
        
        $this->participantProfileRepository = $this->buildMockOfInterface(ParticipantProfileRepository::class);

        $this->service = new RemoveParticipantProfile(
                $this->clientParticipantRepository, $this->participantProfileRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programParticipationId, $this->programsProfileFormId);
    }
    public function test_execute_removeProfileInClientParticipant()
    {
        $this->participantProfileRepository->expects($this->once())
                ->method("aParticipantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programParticipationId, $this->programsProfileFormId);
        
        $this->clientParticipant->expects($this->once())
                ->method("removeProfile");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->participantProfileRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
