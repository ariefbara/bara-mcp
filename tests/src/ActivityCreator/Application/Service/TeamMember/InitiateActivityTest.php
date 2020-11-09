<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\ {
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Team\ProgramParticipation,
    Domain\Model\ParticipantActivity,
    Domain\service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateActivityTest extends TestBase
{
    protected $participantActivityRepository, $nextId = "nextId";
    protected $teamMember, $teamMemberRepository;
    protected $teamParticipantRepository, $programParticipation;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", 
            $programParticipationId = "programParticipationId", $activityTypeId = "activityTypeId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantActivityRepository = $this->buildMockOfInterface(ParticipantActivityRepository::class);
        $this->participantActivityRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository = $this->buildMockOfClass(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);

        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->programParticipationId)
                ->willReturn($this->programParticipation);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->activityTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateActivity(
                $this->participantActivityRepository, $this->teamMemberRepository, $this->teamParticipantRepository, 
                $this->activityTypeRepository, $this->dispatcher);
        
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->programParticipationId, 
                $this->activityTypeId, $this->activityDataProvider);
    }
    public function test_execute_addActivityToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method("initiateActivityInProgramParticipation")
                ->with($this->programParticipation, $this->nextId, $this->activityType, $this->activityDataProvider);
        $this->participantActivityRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheActivity()
    {
        $participantActivity = $this->buildMockOfClass(ParticipantActivity::class);
        $this->teamMember->expects($this->once())
                ->method("initiateActivityInProgramParticipation")
                ->willReturn($participantActivity);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($participantActivity);
        $this->execute();
    }
}
