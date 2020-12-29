<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\Model\UserParticipant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitParticipantProfileTest extends TestBase
{
    protected $userParticipantRepository, $userParticipant;
    protected $programsProfileFormRepository;
    protected $service;
    protected $userId = "userId", $programParticipationId = "userParticipantId",
            $programsProfileFormId = "programProfileFormId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("aUserParticipant")
                ->with($this->userId, $this->programParticipationId)
                ->willReturn($this->userParticipant);
        
        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);

        $this->service = new SubmitParticipantProfile(
                $this->userParticipantRepository, $this->programsProfileFormRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->userId, $this->programParticipationId, $this->programsProfileFormId, $this->formRecordData);
    }
    public function test_execute_submitProfileInClientParticipant()
    {
        $this->programsProfileFormRepository->expects($this->once())->method("ofId");
        
        $this->userParticipant->expects($this->once())
                ->method("submitProfile")
                ->with($this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
