<?php

namespace Firm\Application\Service\Personnel\ProgramCoordinator;

use Firm\Application\Service\Firm\Program\ActivityType\MeetingRepository;
use Firm\Application\Service\Firm\Program\ActivityTypeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{
    protected $meetingRepository, $nextId = "nextId";
    protected $activityTypeRepository, $activityType;
    protected $programCoordinatorRepository, $programCoordinator;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $programId = "programId", $meetingTypeId = "meetingTypeId";
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingRepository = $this->buildMockOfInterface(MeetingRepository::class);
        $this->meetingRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->meetingTypeId)
                ->willReturn($this->activityType);
        
        $this->programCoordinator = $this->buildMockOfClass(Coordinator::class);
        $this->programCoordinatorRepository = $this->buildMockOfInterface(ProgramCoordinatorRepository::class);
        $this->programCoordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->programCoordinator);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->programCoordinatorRepository, $this->activityTypeRepository, $this->dispatcher);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->meetingTypeId, $this->meetingData);
    }
    public function test_execute_addMeetingToRepository()
    {
        $this->programCoordinator->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->nextId, $this->activityType, $this->meetingData);
        $this->meetingRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatchMeeting()
    {
        $meeting = $this->buildMockOfClass(Meeting::class);
        $this->programCoordinator->expects($this->once())
                ->method("initiateMeeting")
                ->willReturn($meeting);
        
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($meeting);
        $this->execute();
    }
}
