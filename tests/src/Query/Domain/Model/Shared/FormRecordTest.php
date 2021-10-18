<?php

namespace Query\Domain\Model\Shared;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Query\Domain\Model\Shared\Form\StringField;
use Query\Domain\Model\Shared\Form\TextAreaField;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;
use Query\Domain\Model\Shared\FormRecord\IntegerFieldRecord;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\SingleSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\StringFieldRecord;
use Query\Domain\Model\Shared\FormRecord\TextAreaFieldRecord;
use Tests\TestBase;

class FormRecordTest extends TestBase
{
    protected $formRecord;
    
    protected $integerFieldRecord;
    protected $stringFieldRecord;
    protected $textAreaFieldRecord;
    protected $attachmentFieldRecord;
    protected $singleSelectFieldRecord;
    protected $multiSelectFieldRecord;
    
    protected $integerField;
    protected $stringField;
    protected $textAreaField;
    protected $attachmentField;
    protected $singleSelectField;
    protected $multiSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecord = new TestableFormRecord();
        
        $this->formRecord->integerFieldRecords = new ArrayCollection();
        $this->formRecord->stringFieldRecords = new ArrayCollection();
        $this->formRecord->textAreaFieldRecords = new ArrayCollection();
        $this->formRecord->attachmentFieldRecords = new ArrayCollection();
        $this->formRecord->singleSelectFieldRecords = new ArrayCollection();
        $this->formRecord->multiSelectFieldRecords = new ArrayCollection();
        
        $this->integerFieldRecord = $this->buildMockOfClass(IntegerFieldRecord::class);
        $this->stringFieldRecord = $this->buildMockOfClass(StringFieldRecord::class);
        $this->textAreaFieldRecord = $this->buildMockOfClass(TextAreaFieldRecord::class);
        $this->attachmentFieldRecord = $this->buildMockOfClass(AttachmentFieldRecord::class);
        $this->singleSelectFieldRecord = $this->buildMockOfClass(SingleSelectFieldRecord::class);
        $this->multiSelectFieldRecord = $this->buildMockOfClass(MultiSelectFieldRecord::class);
        
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->attachmentField = $this->buildMockOfClass(AttachmentField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
    }
    
    protected function getStringRecordValueCorrespondWith()
    {
        $this->formRecord->stringFieldRecords->add($this->stringFieldRecord);
        $this->stringFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->stringField)
                ->willReturn(true);
        return $this->formRecord->getStringFieldRecordValueCorrespondWith($this->stringField);
    }
    public function test_getStringFieldRecordValueCorrespondWith_returnValue()
    {
        $this->stringFieldRecord->expects($this->any())
                ->method('getValue')
                ->willReturn($stringValue = 'string value');
        $this->assertEquals($stringValue, $this->getStringRecordValueCorrespondWith());
    }
    public function test_getStringFieldRecordValueCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->stringFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->stringField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getStringRecordValueCorrespondWith());
    }
    
    protected function getIntegerRecordValueCorrespondWith()
    {
        $this->formRecord->integerFieldRecords->add($this->integerFieldRecord);
        $this->integerFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->integerField)
                ->willReturn(true);
        return $this->formRecord->getIntegerFieldRecordValueCorrespondWith($this->integerField);
    }
    public function test_getIntegerFieldRecordValueCorrespondWith_returnValue()
    {
        $this->integerFieldRecord->expects($this->any())
                ->method('getValue')
                ->willReturn($integerValue = 999.99);
        $this->assertEquals($integerValue, $this->getIntegerRecordValueCorrespondWith());
    }
    public function test_getIntegerFieldRecordValueCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->integerFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->integerField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getIntegerRecordValueCorrespondWith());
    }
    
    protected function getTextAreaRecordValueCorrespondWith()
    {
        $this->formRecord->textAreaFieldRecords->add($this->textAreaFieldRecord);
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->textAreaField)
                ->willReturn(true);
        return $this->formRecord->getTextAreaFieldRecordValueCorrespondWith($this->textAreaField);
    }
    public function test_getTextAreaFieldRecordValueCorrespondWith_returnValue()
    {
        $this->textAreaFieldRecord->expects($this->any())
                ->method('getValue')
                ->willReturn($textAreaValue = 'string represent text area value');
        $this->assertEquals($textAreaValue, $this->getTextAreaRecordValueCorrespondWith());
    }
    public function test_getTextAreaFieldRecordValueCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->textAreaField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getTextAreaRecordValueCorrespondWith());
    }
    
    protected function getFileInfoListOfAttachmentFieldRecordCorrespondWith()
    {
        $this->formRecord->attachmentFieldRecords->add($this->attachmentFieldRecord);
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->attachmentField)
                ->willReturn(true);
        return $this->formRecord->getFileInfoListOfAttachmentFieldRecordCorrespondWith($this->attachmentField);
    }
    public function test_getFileInfoListOfAttachmentFieldRecordCorrespondWith_returnAttachedFileLocationList()
    {
        $this->attachmentFieldRecord->expects($this->any())
                ->method('getStringOfAttachedFileLocationList')
                ->willReturn($attachmentValue = 'string represent text area value');
        $this->assertEquals($attachmentValue, $this->getFileInfoListOfAttachmentFieldRecordCorrespondWith());
    }
    public function test_getFileInfoListOfAttachmentFieldRecordCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->attachmentField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getFileInfoListOfAttachmentFieldRecordCorrespondWith());
    }
    
    protected function getSingleSelectFieldRecordSelectedOptionNameCorrespondWith()
    {
        $this->formRecord->singleSelectFieldRecords->add($this->singleSelectFieldRecord);
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->singleSelectField)
                ->willReturn(true);
        return $this->formRecord->getSingleSelectFieldRecordSelectedOptionNameCorrespondWith($this->singleSelectField);
    }
    public function test_getSingleSelectFieldRecordSelectedOptionNameCorrespondWith_returnSelectedOptionName()
    {
        $this->singleSelectFieldRecord->expects($this->any())
                ->method('getSelectedOptionName')
                ->willReturn($singleSelectValue = 'selected option name');
        $this->assertEquals($singleSelectValue, $this->getSingleSelectFieldRecordSelectedOptionNameCorrespondWith());
    }
    public function test_getSingleSelectFieldRecordSelectedOptionNameCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->singleSelectField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getSingleSelectFieldRecordSelectedOptionNameCorrespondWith());
    }
    
    protected function getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith()
    {
        $this->formRecord->multiSelectFieldRecords->add($this->multiSelectFieldRecord);
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->multiSelectField)
                ->willReturn(true);
        return $this->formRecord->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($this->multiSelectField);
    }
    public function test_getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith_returnSelectedOptionName()
    {
        $this->multiSelectFieldRecord->expects($this->any())
                ->method('getStringOfSelectedOptionNameList')
                ->willReturn($multiSelectValue = 'list of selected option name');
        $this->assertEquals($multiSelectValue, $this->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith());
    }
    public function test_getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith_noCorrespondingRecord_returnNull()
    {
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isActiveFieldRecordCorrespondWith')
                ->with($this->multiSelectField)
                ->willReturn(false);
        $this->assertEquals(null, $this->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith());
    }
}

class TestableFormRecord extends FormRecord
{
    public $form;
    public $id;
    public $submitTime;
    public $integerFieldRecords;
    public $stringFieldRecords;
    public $textAreaFieldRecords;
    public $singleSelectFieldRecords;
    public $multiSelectFieldRecords;
    public $attachmentFieldRecords;
    
    function __construct()
    {
        parent::__construct();
    }
}
