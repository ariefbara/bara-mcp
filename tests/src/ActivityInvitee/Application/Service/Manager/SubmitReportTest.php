<?php

namespace ActivityInvitee\Application\Service\Manager;

use ActivityInvitee\Domain\Model\ManagerInvitee;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitReportTest extends TestBase
{

    protected $activityInvitationRepository, $invitation;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $invitationId = "invitationId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(ManagerInvitee::class);
        $this->activityInvitationRepository = $this->buildMockOfInterface(ActivityInvitationRepository::class);
        $this->activityInvitationRepository->expects($this->any())
                ->method("anInvitationBelongsToManager")
                ->with($this->firmId, $this->managerId, $this->invitationId)
                ->willReturn($this->invitation);

        $this->service = new SubmitReport($this->activityInvitationRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->invitationId, $this->formRecordData);
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
