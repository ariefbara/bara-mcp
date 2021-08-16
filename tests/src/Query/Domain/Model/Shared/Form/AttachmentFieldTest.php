<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class AttachmentFieldTest extends TestBase
{
    protected $attachmentField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->attachmentField = new TestableAttachmentField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfAttachmentFieldRecordCorrespondWithAttachmentField()
    {
        $this->formRecord->expects($this->once())
                ->method('getFileInfoListOfAttachmentFieldRecordCorrespondWith')
                ->with($this->attachmentField);
        $this->attachmentField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableAttachmentField extends AttachmentField
{
    public $form;
    public $id;
    public $fieldVO;
    public $minMaxValue;
    public $removed = false;
    
    public function __construct()
    {
        
    }
}
