<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Mission,
    Model\Participant
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class CompletedMissionTest extends TestBase
{
    protected $participant;
    protected $mission;
    protected $completedMission;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->completedMission = new TestableCompletedMission($this->participant, "id", $this->mission);
    }
    
    public function test_construct_setProperties()
    {
        $completedMission = new TestableCompletedMission($this->participant, $this->id, $this->mission);
        $this->assertEquals($this->participant, $completedMission->participant);
        $this->assertEquals($this->id, $completedMission->id);
        $this->assertEquals($this->mission, $completedMission->mission);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $completedMission->completedTime);
    }
    
    public function test_correspondWithMission_sameMission_returnTrue()
    {
        $this->assertTrue($this->completedMission->correspondWithMission($this->mission));
    }
    public function test_correspondWithMission_differentMission_returnFalse()
    {
        $mission = $this->buildMockOfClass(Mission::class);
        $this->assertFalse($this->completedMission->correspondWithMission($mission));
    }
}

class TestableCompletedMission extends CompletedMission
{
    public $participant;
    public $id;
    public $mission;
    public $completedTime;
}
