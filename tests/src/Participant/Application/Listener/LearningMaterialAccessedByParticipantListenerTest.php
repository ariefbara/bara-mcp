<?php

namespace Participant\Application\Listener;

use Participant\Application\Service\Participant\LogViewLearningMaterialActivity;
use Tests\TestBase;

class LearningMaterialAccessedByParticipantListenerTest extends TestBase
{
    protected $logViewLearningMaterialActivity;
    protected $listener;
    protected $event, $participantId = "participantId", $learningMaterialId = "learningMaterialid";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->logViewLearningMaterialActivity = $this->buildMockOfClass(LogViewLearningMaterialActivity::class);
        $this->listener = new LearningMaterialAccessedByParticipantListener($this->logViewLearningMaterialActivity);
        $this->event = $this->buildMockOfInterface(EventTriggeredByParticipantInterface::class);
        $this->event->expects($this->any())->method("getParticipantId")->willReturn($this->participantId);
        $this->event->expects($this->any())->method("getId")->willReturn($this->learningMaterialId);
    }
    
    public function test_handle_executeService()
    {
        $this->logViewLearningMaterialActivity->expects($this->once())
                ->method("execute")
                ->with($this->participantId, $this->learningMaterialId);
        $this->listener->handle($this->event);
    }
    
}
