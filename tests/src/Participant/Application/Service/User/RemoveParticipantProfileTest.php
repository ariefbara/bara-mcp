<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ParticipantProfileRepository;
use Participant\Domain\Model\UserParticipant;
use Tests\TestBase;

class RemoveParticipantProfileTest extends TestBase
{
    protected $userParticipantRepository, $userParticipant;
    protected $participantProfileRepository;
    protected $service;
    protected $firmId = "firmId", $userId = "userId", $programParticipationId = "userParticipantId",
            $programsProfileFormId = "programProfileFormId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("aUserParticipant")
                ->with($this->userId, $this->programParticipationId)
                ->willReturn($this->userParticipant);

        $this->participantProfileRepository = $this->buildMockOfInterface(ParticipantProfileRepository::class);

        $this->service = new RemoveParticipantProfile($this->participantProfileRepository, $this->userParticipantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->programParticipationId, $this->programsProfileFormId);
    }
    public function test_execute_removeProfileInUserParticipant()
    {
        $this->participantProfileRepository->expects($this->once())
                ->method("aParticipantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programParticipationId, $this->programsProfileFormId);
        $this->userParticipant->expects($this->once())
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
