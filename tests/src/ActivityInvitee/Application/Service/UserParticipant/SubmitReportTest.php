<?php

namespace ActivityInvitee\Application\Service\UserParticipant;

use ActivityInvitee\Domain\Model\ParticipantInvitee;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitReportTest extends TestBase
{
    protected $activityInvitationRepository, $invitation;
    protected $service;
    protected $userId = "userId", $invitationId = "invitationId";
    protected $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(ParticipantInvitee::class);
        $this->activityInvitationRepository = $this->buildMockOfInterface(ActivityInvitationRepository::class);
        $this->activityInvitationRepository->expects($this->any())
                ->method("anInvitationBelongsToUser")
                ->with($this->userId, $this->invitationId)
                ->willReturn($this->invitation);
        
        $this->service = new SubmitReport($this->activityInvitationRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->invitationId, $this->formRecordData);
    }
    public function test_execute_submitReportInInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->activityInvitationRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
