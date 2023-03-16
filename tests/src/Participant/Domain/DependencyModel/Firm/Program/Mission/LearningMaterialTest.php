<?php

namespace Participant\Domain\DependencyModel\Firm\Program\Mission;

use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\Participant;
use Tests\TestBase;

class LearningMaterialTest extends TestBase
{
    protected $learningMaterial, $mission;
    //
    protected $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterial = new TestableLearningMaterial();
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->learningMaterial->mission = $this->mission;
        //
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    //
    protected function assertAccessibleByParticipant()
    {
        $this->mission->expects($this->any())
                ->method('isSameProgramAsParticipant')
                ->with($this->participant)
                ->willReturn(true);
        $this->learningMaterial->assertAccessibleByParticipant($this->participant);
    }
    public function test_assertAccessibleByParticipant_missionProgramDiffFromParticipant_forbidden()
    {
        $this->mission->expects($this->any())
                ->method('isSameProgramAsParticipant')
                ->with($this->participant)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(fn() => $this->assertAccessibleByParticipant(), 'Forbidden', 'inaccessible learning material');
    }
    public function test_assertAccessibleByParticipant_missionProgramSameAsParticipant_void()
    {
        $this->assertAccessibleByParticipant();
        $this->markAsSuccess();
    }
    public function test_assertAccessibleByParticipant_removedLearningMaterial_forbidden()
    {
        $this->learningMaterial->removed = true;
        $this->assertRegularExceptionThrowed(fn() => $this->assertAccessibleByParticipant(), 'Forbidden', 'inaccessible learning material');
    }
}

class TestableLearningMaterial extends LearningMaterial
{
    public $mission;
    public $id = 'learningMaterialId';
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
