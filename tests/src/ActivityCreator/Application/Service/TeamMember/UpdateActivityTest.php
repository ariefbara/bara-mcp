<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateActivityTest extends TestBase
{
    protected $participantActivityRepository, $participantActivity;
    protected $teamMember, $teamMemberRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", 
            $participantActivityId = "participantActivityId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantActivity = $this->buildMockOfClass(ParticipantActivity::class);
        $this->participantActivityRepository = $this->buildMockOfInterface(ParticipantActivityRepository::class);
        $this->participantActivityRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantActivityId)
                ->willReturn($this->participantActivity);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UpdateActivity($this->participantActivityRepository, $this->teamMemberRepository, $this->dispatcher);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->participantActivityId, $this->activityDataProvider);
    }
    public function test_execute_updateParticipantActivity()
    {
        $this->teamMember->expects($this->once())
                ->method("updateActivity")
                ->with($this->participantActivity, $this->activityDataProvider);
        $this->execute();
    }
    public function test_updateRepository()
    {
        $this->participantActivityRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

    public function test_execute_dispathceParticipantActivity()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch");
        $this->execute();
    }
}
