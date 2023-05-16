<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\TestBase;

class KeyResultProgressReportAttachmentTest extends TestBase
{
    protected $keyResultProgressReport;
    protected $fileInfo;
    //
    protected $attachment;
    //
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->keyResultProgressReport = $this->buildMockOfClass(KeyResultProgressReport::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        //
        $this->attachment = new TestableKeyResultProgressReportAttachment($this->keyResultProgressReport, $this->fileInfo, 'id');
        $this->attachment->relevantAttachment = false;
    }
    
    //
    protected function construct()
    {
        return new TestableKeyResultProgressReportAttachment($this->keyResultProgressReport, $this->fileInfo, $this->id);
    }
    public function test_construct_setProperties()
    {
        $attachment = $this->construct();
        $this->assertSame($this->keyResultProgressReport, $attachment->keyResultProgressReport);
        $this->assertSame($this->fileInfo, $attachment->fileInfo);
        $this->assertSame($this->id, $attachment->id);
        $this->assertFalse($attachment->removed);
    }
    public function test_construct_setRelevant()
    {
        $attachment = $this->construct();
        $this->assertTrue($attachment->relevantAttachment);
    }
    
    //
    protected function renew()
    {
        $this->attachment->renew();
    }
    public function test_renew_setUnremoved()
    {
        $this->attachment->removed = true;
        $this->renew();
        $this->assertFalse($this->attachment->removed);
    }
    public function test_renew_setRelevant()
    {
        $this->renew();
        $this->assertTrue($this->attachment->relevantAttachment);
    }
    
    //
    protected function removeIfIrrelevant()
    {
        $this->attachment->removeIfIrrelevant();
    }
    public function test_removeIfIrrelevant_setRemoved()
    {
        $this->removeIfIrrelevant();
        $this->assertTrue($this->attachment->removed);
    }
    public function test_removeIfIrrelevant_relevantAttachment_void()
    {
        $this->attachment->relevantAttachment = true;
        $this->removeIfIrrelevant();
        $this->assertFalse($this->attachment->removed);
    }
    
    //
    protected function fileInfoEquals()
    {
        return $this->attachment->fileInfoEquals($this->fileInfo);
    }
    public function test_fileInfoEquals_sameFileInfo_returnTrue()
    {
        $this->assertTrue($this->fileInfoEquals());
    }
    public function test_fileInfoEquals_diffFileInfo_returnTrue()
    {
        $this->attachment->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->assertFalse($this->fileInfoEquals());
    }
}

class TestableKeyResultProgressReportAttachment extends KeyResultProgressReportAttachment
{

    public $keyResultProgressReport;
    public $fileInfo;
    public $id;
    public $removed;
    public $relevantAttachment;

}
