<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant
};
use Tests\TestBase;

class ViewLearningMaterialActivityLogTest extends TestBase
{
    protected $participant;
    protected $id = "newId", $learningMaterialId = "newLearningMaterialId";
    protected $teamMember;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $log = new TestableViewLearningMaterialActivityLog(
                $this->participant, $this->id, $this->learningMaterialId, $this->teamMember);
        $this->assertEquals($this->participant, $log->participant);
        $this->assertEquals($this->id, $log->id);
        $this->assertEquals($this->learningMaterialId, $log->learningMaterialId);
        
        $message = "accessed learning material";
        $activityLog = new \Participant\Domain\SharedModel\ActivityLog($this->id, $message, $this->teamMember);
        
    }
}

class TestableViewLearningMaterialActivityLog extends ViewLearningMaterialActivityLog
{
    public $participant;
    public $id;
    public $learningMaterialId;
    public $activityLog;
}
