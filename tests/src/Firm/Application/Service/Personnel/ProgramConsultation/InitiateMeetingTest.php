<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Personnel\MeetingRepository,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{
    protected $meetingRepository, $nextId = "nextId";
    protected $activityTypeRepository, $activityType;
    protected $programConsultantRepository, $programConsultant;
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
        
        $this->programConsultant = $this->buildMockOfClass(Consultant::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultantRepository->expects($this->any())
                ->method("aConsultantCorrespondWithProgram")
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->programConsultant);
        
        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->programConsultantRepository, $this->activityTypeRepository);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->meetingTypeId, $this->meetingData);
    }
    public function test_execute_addMeetingToRepository()
    {
        $this->programConsultant->expects($this->once())
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
