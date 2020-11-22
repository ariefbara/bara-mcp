<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{
    protected $meetingRepository, $nextId = "nextId";
    protected $activityTypeRepository, $activityType;
    protected $clientParticipantRepository, $clientParticipant;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $programId = "programId", $meetingTypeId = "meetingTypeId";
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
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method("aClientParticipantCorrespondWithProgram")
                ->with($this->firmId, $this->clientId, $this->programId)
                ->willReturn($this->clientParticipant);
        
        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->clientParticipantRepository, $this->activityTypeRepository);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->meetingTypeId, $this->meetingData);
    }
    public function test_execute_addMeetingToRepository()
    {
        $this->clientParticipant->expects($this->once())
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
