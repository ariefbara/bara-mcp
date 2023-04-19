<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport\KeyResultProgressReportAttachment;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\TestBase;

class KeyResultProgressReportTest extends TestBase
{
    protected $objectiveProgressReport;
    protected $keyResult;
    //
    protected $keyResultProgressReport, $keyResultProgressReportAttachment;
    //
    protected $id = 'newId';
    protected $value = 99;
    protected $fileInfoOne;


    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->keyResult = $this->buildMockOfClass(KeyResult::class);
        $keyResultProgressReportData = new KeyResultProgressReportData(11);
        //
        $this->keyResultProgressReport = new TestableKeyResultProgressReport(
                $this->objectiveProgressReport, $this->keyResult, 'id', $keyResultProgressReportData);
        
        $this->keyResultProgressReportAttachment = $this->buildMockOfClass(KeyResultProgressReportAttachment::class);
        $this->keyResultProgressReport->attachments = new ArrayCollection();
        $this->keyResultProgressReport->attachments->add($this->keyResultProgressReportAttachment);
        //
        $this->fileInfoOne = $this->buildMockOfClass(FileInfo::class);
    }
    protected function getKeyResultProgressReportData()
    {
        return (new KeyResultProgressReportData($this->value))
            ->addAttachment($this->fileInfoOne);
    }
    
    protected function executeConstruct()
    {
        return new TestableKeyResultProgressReport(
                $this->objectiveProgressReport, $this->keyResult, $this->id, $this->getKeyResultProgressReportData());
    }
    public function test_construct_setProperties()
    {
        $keyResultProgressReport = $this->executeConstruct();
        $this->assertEquals($this->objectiveProgressReport, $keyResultProgressReport->objectiveProgressReport);
        $this->assertEquals($this->keyResult, $keyResultProgressReport->keyResult);
        $this->assertEquals($this->id, $keyResultProgressReport->id);
        $this->assertEquals($this->value, $keyResultProgressReport->value);
        $this->assertFalse($keyResultProgressReport->disabled);
    }
    public function test_construct_addAttachments()
    {
        $keyResultProgressReport = $this->executeConstruct();
        $this->assertEquals(1, $keyResultProgressReport->attachments->count());
        $this->assertInstanceOf(KeyResultProgressReportAttachment::class, $keyResultProgressReport->attachments->first());
    }
    
    //
    protected function executeUpdate()
    {
        $this->keyResultProgressReport->update($this->getKeyResultProgressReportData());
    }
    public function test_update_udpateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->value, $this->keyResultProgressReport->value);
    }
    public function test_update_disabled_setEnable()
    {
        $this->keyResultProgressReport->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->keyResultProgressReport->disabled);
    }
    public function test_update_addNewAttachments()
    {
        $this->executeUpdate();
        $this->assertEquals(2, $this->keyResultProgressReport->attachments->count());
        $this->assertInstanceOf(KeyResultProgressReportAttachment::class, $this->keyResultProgressReport->attachments->last());
    }
    public function test_update_alreadyHasAttachmentWithSameFileInfo_renewAttachment()
    {
        $this->keyResultProgressReportAttachment->expects($this->once())
                ->method('fileInfoEquals')
                ->with($this->fileInfoOne)
                ->willReturn(true);
        $this->keyResultProgressReportAttachment->expects($this->once())
                ->method('renew');
        $this->executeUpdate();
    }
    public function test_update_alreadyHasAttachmentWithSameFileInfo_preventAddNewAttachment()
    {
        $this->keyResultProgressReportAttachment->expects($this->once())
                ->method('fileInfoEquals')
                ->with($this->fileInfoOne)
                ->willReturn(true);
        $this->executeUpdate();
        $this->assertEquals(1, $this->keyResultProgressReport->attachments->count());
    }
    public function test_update_removeIrrelevantAttachment()
    {
        $this->keyResultProgressReportAttachment->expects($this->once())
                ->method('removeIfIrrelevant');
        $this->executeUpdate();
    }
    
    //
    protected function executeDisableIfCorrespondWithInactiveKeyResult()
    {
        $this->keyResultProgressReport->disableIfCorrespondWithInactiveKeyResult();
    }
    public function test_disableIfCorrespondWithInactiveKeyResult_setDisabled()
    {
        $this->executeDisableIfCorrespondWithInactiveKeyResult();
        $this->assertTrue($this->keyResultProgressReport->disabled);
    }
    public function test_disableIfCorrespondWithInactiveKeyResult_activeKeyresult_NOP()
    {
        $this->keyResult->expects($this->any())->method('isActive')->willReturn(true);
        $this->executeDisableIfCorrespondWithInactiveKeyResult();
        $this->assertFalse($this->keyResultProgressReport->disabled);
    }
    
    public function test_correspondWith_sameKeyResult_returnTrue()
    {
        $this->assertTrue($this->keyResultProgressReport->correspondWith($this->keyResult));
    }
    public function test_correspondWith_differentKeyResult_returnFalse()
    {
        $this->assertFalse($this->keyResultProgressReport->correspondWith($this->buildMockOfClass(KeyResult::class)));
    }
}

class TestableKeyResultProgressReport extends KeyResultProgressReport
{
    public $objectiveProgressReport;
    public $keyResult;
    public $id;
    public $value;
    public $disabled;
    public $attachments;
}
