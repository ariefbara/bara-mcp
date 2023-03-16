<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;
use Participant\Domain\Model\Participant;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class LearningProgressTest extends TestBase
{
    protected $participant;
    protected $learningMaterial;
    protected $learningProgress;
    //
    protected $id = 'newId', $progressMark = 'new progress mark', $markAsCompleted = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->learningMaterial = $this->buildMockOfClass(LearningMaterial::class);
        
        $data = (new LearningProgressData())
                ->setProgressMark('old progress mark')
                ->setMarkAsCompleted(false);
        $this->learningProgress = new TestableLearningProgress($this->participant, $this->learningMaterial, 'id', $data);
        $this->learningProgress->lastModifiedTime = new \DateTimeImmutable('-1 months');
        //
    }
    
    //
    protected function getLearningProgressData()
    {
        return (new LearningProgressData())
        ->setMarkAsCompleted($this->markAsCompleted)
                ->setProgressMark($this->progressMark);
    }
    
    //
    protected function construct()
    {
        return new TestableLearningProgress($this->participant, $this->learningMaterial, $this->id, $this->getLearningProgressData());
    }
    public function test_construct_setProperties()
    {
        $learningProgress = $this->construct();
        $this->assertSame($this->participant, $learningProgress->participant);
        $this->assertSame($this->learningMaterial, $learningProgress->learningMaterial);
        $this->assertSame($this->id, $learningProgress->id);
        $this->assertSame($this->progressMark, $learningProgress->progressMark);
        $this->assertSame($this->markAsCompleted, $learningProgress->markAsCompleted);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $learningProgress->lastModifiedTime);
    }
    // value already converted in data
    public function test_construct_nonBooleanMark_castToBoolean()
    {
        $this->markAsCompleted = 'non boolean value';
        $learningProgress = $this->construct();
        $this->assertSame(true, $learningProgress->markAsCompleted);
    }
    
    //
    protected function update()
    {
        $this->learningProgress->update($this->getLearningProgressData());
    }
    public function test_update_updateProperties()
    {
        $this->update();
        $this->assertSame($this->progressMark, $this->learningProgress->progressMark);
        $this->assertSame($this->markAsCompleted, $this->learningProgress->markAsCompleted);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->learningProgress->lastModifiedTime);
    }
    
    //
    protected function updateProgressMark()
    {
        $this->learningProgress->updateProgressMark($this->progressMark);
    }
    public function test_updateProgressMark_updateProgressMarkAndModifiedTime()
    {
        $this->updateProgressMark();
        $this->assertSame($this->progressMark, $this->learningProgress->progressMark);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->learningProgress->lastModifiedTime);
    }
    
    //
    protected function markComplete()
    {
        $this->learningProgress->markComplete();
    }
    public function test_markComplete_markAsCompletedAndUpdateModifiedTime()
    {
        $this->markComplete();
        $this->assertTrue($this->learningProgress->markAsCompleted);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->learningProgress->lastModifiedTime);
    }
    public function test_markAsCompleted_alreadyCompleted_keepModifiedTime()
    {
        $oldModifiedTime = $this->learningProgress->lastModifiedTime;
        
        $this->learningProgress->markAsCompleted = true;
        $this->markComplete();
        $this->assertEquals($oldModifiedTime, $this->learningProgress->lastModifiedTime);
    }
    
    //
    protected function unmarkCompleteStatus()
    {
        $this->learningProgress->unmarkCompleteStatus();
    }
    public function test_unmarkCompleteStatus_setUnmarked()
    {
        $this->learningProgress->markAsCompleted = true;
        
        $this->unmarkCompleteStatus();
        $this->assertFalse($this->learningProgress->markAsCompleted);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->learningProgress->lastModifiedTime);
    }
    public function test_unmarkCompleteStatus_notCompleted_keepModifiedTime()
    {
        $oldModifiedTime = $this->learningProgress->lastModifiedTime;
        
        $this->unmarkCompleteStatus();
        $this->assertFalse($this->learningProgress->markAsCompleted);
        $this->assertEquals($oldModifiedTime, $this->learningProgress->lastModifiedTime);
    }
    
    //
    protected function isAssociateWithLearningMaterial()
    {
        return $this->learningProgress->isAssociateWithLearningMaterial($this->learningMaterial);
    }
    public function test_isAssociateWithLearningMaterial_sameMaterial_returnTrue()
    {
        $this->assertTrue($this->isAssociateWithLearningMaterial());
    }
    public function test_isAssociateWithLearningMaterial_diffMaterial_returnFalse()
    {
        $this->learningProgress->learningMaterial = $this->buildMockOfClass(LearningMaterial::class);
        $this->assertFalse($this->isAssociateWithLearningMaterial());
    }
    
    //
    protected function assertManageableByParticipant()
    {
        return $this->learningProgress->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_diffParticipant_forbidden()
    {
        $this->learningProgress->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(fn() => $this->assertManageableByParticipant(), 'Forbidden', 'unmanaged learning progress');
    }
    public function test_assertManageableByParticipant_sameParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
}

class TestableLearningProgress extends LearningProgress
{

    public $participant;
    public $learningMaterial;
    public $id;
    public $lastModifiedTime;
    public $progressMark;
    public $markAsCompleted;

}
