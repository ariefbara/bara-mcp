<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\MeetingType\MeetingData,
    Domain\Model\Firm\Program\UserParticipant
};
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{
    protected $meetingRepository, $nextId = "nextId";
    protected $activityTypeRepository, $activityType;
    protected $userParticipantRepository, $userParticipant;
    protected $service;
    protected $userId = "userId", $programId = "programId", $meetingTypeId = "meetingTypeId";
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
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("aUserParticipantCorrespondWithProgram")
                ->with($this->userId, $this->programId)
                ->willReturn($this->userParticipant);
        
        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->userParticipantRepository, $this->activityTypeRepository);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->userId, $this->programId, $this->meetingTypeId, $this->meetingData);
    }
    public function test_execute_addMeetingToRepository()
    {
        $this->userParticipant->expects($this->once())
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
}
