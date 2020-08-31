<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use Resources\Domain\ValueObject\IntegerRange;
use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class AttachmentFieldTest extends TestBase
{

    protected $attachmentField;
    protected $id = 'attachmentFieldId', $field, $minMaxValue;
    protected $formRecord, $formRecordData, $fileInfo, $fileInfoList;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attachmentField = new TestableAttachmentField();
        $this->attachmentField->id = $this->id;
        $this->field = $this->buildMockOfClass(FieldVO::class);
        $this->attachmentField->field = $this->field;
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->attachmentField->minMaxValue = $this->minMaxValue;
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoList = [$this->fileInfo];
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecordData->expects($this->any())
            ->method('getAttachedFileInfoListOf')
            ->with($this->attachmentField->id)
            ->willReturn($this->fileInfoList);
    }
    
    protected function executeSetAttachmentFieldRecordOf()
    {
        $this->minMaxValue->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->attachmentField->setAttachmentFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setAttachmentFieldRecordOf_executeFormRecordsSetAttachmentFieldRecord()
    {
        $this->formRecord->expects($this->once())
            ->method('setAttachmentFieldRecord')
            ->with($this->attachmentField, $this->fileInfoList);
        $this->executeSetAttachmentFieldRecordOf();
    }
    public function test_setAttachmentFieldRecordOf_executeFieldAssertRequirementSatisfiedMethod()
    {
        $this->field->expects($this->once())
            ->method('assertMandatoryRequirementSatisfied')
            ->with($this->fileInfoList);
        $this->executeSetAttachmentFieldRecordOf();
    }
    public function test_setAttachmentFieldRecordOf_fileInfoCountNotInMinMaxValueRange_throwEx()
    {
        $this->minMaxValue->expects($this->once())
                ->method('contain')
                ->with(count($this->fileInfoList))
                ->willReturn(false);
        $operation = function () {
            $this->executeSetAttachmentFieldRecordOf();
        };
        $errorDetail = "bad request: attached file count for {$this->attachmentField->field->getName()} is out of range";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

}

class TestableAttachmentField extends AttachmentField
{

    public $assignmentForm;
    public $id;
    public $field;
    public $minMaxValue;
    public $minMaxSize;
    public $removed;

    function __construct()
    {
        parent::__construct();
    }
}
