<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\Model\ClientParticipant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitParticipantProfileTest extends TestBase
{
    protected $clientParticipantRepository, $clientParticipant;
    protected $programsProfileFormRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $programParticipationId = "clientParticipantId",
            $programsProfileFormId = "programProfileFormId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method("aClientParticipant")
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);
        
        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);

        $this->service = new SubmitParticipantProfile(
                $this->clientParticipantRepository, $this->programsProfileFormRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->programParticipationId, $this->programsProfileFormId, $this->formRecordData);
    }
    public function test_execute_submitProfileInClientParticipant()
    {
        $this->programsProfileFormRepository->expects($this->once())->method("ofId");
        
        $this->clientParticipant->expects($this->once())
                ->method("submitProfile")
                ->with($this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
