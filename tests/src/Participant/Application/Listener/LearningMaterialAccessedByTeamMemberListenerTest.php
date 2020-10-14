<?php

namespace Participant\Application\Listener;

use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\LogViewLearningMaterialActivity;
use Tests\TestBase;

class LearningMaterialAccessedByTeamMemberListenerTest extends TestBase
{
    protected $logViewLearningMaterialActivity;
    protected $listener;
    protected $event, $teamMemberId = "teamMemberId", $participantId = "participantId", $learningMaterialid = "learningMaterialId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->logViewLearningMaterialActivity = $this->buildMockOfClass(LogViewLearningMaterialActivity::class);
        $this->listener = new LearningMaterialAccessedByTeamMemberListener($this->logViewLearningMaterialActivity);
        
        $this->event = $this->buildMockOfInterface(EventTriggeredByTeamMemberInterface::class);
        $this->event->expects($this->any())->method("getTeamMemberId")->willReturn($this->teamMemberId);
        $this->event->expects($this->any())->method("getParticipantId")->willReturn($this->participantId);
        $this->event->expects($this->any())->method("getid")->willReturn($this->learningMaterialid);
    }
    
    public function test_handle_executeService()
    {
        $this->logViewLearningMaterialActivity->expects($this->once())
                ->method("execute")
                ->with($this->teamMemberId, $this->participantId, $this->learningMaterialid);
        $this->listener->handle($this->event);
    }
}
