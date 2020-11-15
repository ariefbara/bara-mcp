<?php

namespace SharedContext\Domain\Model\SharedEntity;

use Doctrine\Common\Collections\ArrayCollection;
use SharedContext\Domain\Model\SharedEntity\Form\ {
    AttachmentField,
    IntegerField,
    MultiSelectField,
    SingleSelectField,
    StringField,
    TextAreaField
};
use Tests\TestBase;

class FormTest extends TestBase
{

    protected $form;
    protected $id = 'new-form-id', $name = 'new name', $description = 'new description';
    protected $stringField, $integerField, $textAreaField, $singleSelectField, $multiSelectField, $attachmentField;
    
    protected $formRecordId = "formRecordId", $formRecord, $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = new TestableForm('id', 'name', 'description');
        
        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        $this->attachmentField = $this->buildMockOfClass(AttachmentField::class);
        
        $this->form->stringFields = new ArrayCollection();
        $this->form->integerFields = new ArrayCollection();
        $this->form->textAreaFields = new ArrayCollection();
        $this->form->singleSelectFields = new ArrayCollection();
        $this->form->multiSelectFields = new ArrayCollection();
        $this->form->attachmentFields = new ArrayCollection();
        
        $this->form->stringFields->add($this->stringField);
        $this->form->integerFields->add($this->integerField);
        $this->form->textAreaFields->add($this->textAreaField);
        $this->form->singleSelectFields->add($this->singleSelectField);
        $this->form->multiSelectFields->add($this->multiSelectField);
        $this->form->attachmentFields->add($this->attachmentField);
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function executeSetFieldRecordsOf()
    {
        $this->form->setFieldRecordsOf($this->formRecord, $this->formRecordData);
    }
    
    public function test_setFieldRecordsOf_executeAllStringFieldSetStringFieldRecordOfMethod()
    {
        $this->stringField->expects($this->once())
            ->method('setStringFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedStringFieldInCollection_ignoreThisRemovedField()
    {
        $this->stringField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->stringField->expects($this->never())
            ->method('setStringFieldRecordOf');
        $this->executeSetFieldRecordsOf();
        
    }

    public function test_setFieldRecordsOf_executeAllIntegerFieldSetIntegerFieldRecordOfMethod()
    {
        $this->integerField->expects($this->once())
            ->method('setIntegerFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedIntegerField_ignoreThisRemovedField()
    {
        $this->integerField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->integerField->expects($this->never())
            ->method('setIntegerFieldRecordOf');
        $this->executeSetFieldRecordsOf();
    }
    
    public function test_setFieldRecordsOf_executeAllTextAreaFieldSetTextAreaFieldRecordOfMethod()
    {
        $this->textAreaField->expects($this->once())
            ->method('setTextAreaFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedTextAreaField_ignoreThisRemovedField()
    {
        $this->textAreaField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->textAreaField->expects($this->never())
            ->method('setTextAreaFieldRecordOf');
        $this->executeSetFieldRecordsOf();
    }
    
    public function test_setFieldRecordsOf_executeAllSingleSelectFieldsSetSingleSelectFieldRecordOfMethod()
    {
        $this->singleSelectField->expects($this->once())
            ->method('setSingleSelectFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedSingleSelectField_ignoreThisRemovedField()
    {
        $this->singleSelectField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->singleSelectField->expects($this->never())
            ->method('setSingleSelectFieldRecordOf');
        $this->executeSetFieldRecordsOf();
    }
    
    public function test_setFieldRecordsOf_executeAllMultiSelectFieldsSetMultiSelectFieldRecordOfMethod()
    {
        $this->multiSelectField->expects($this->once())
            ->method('setMultiSelectFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedMultiSelectField_ignoreThisRemovedField()
    {
        $this->multiSelectField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->multiSelectField->expects($this->never())
            ->method('setMultiSelectFieldRecordOf');
        $this->executeSetFieldRecordsOf();
    }
    
    public function test_setFieldRecordsOf_executeAllAttachmentFieldsSetAttachmentFieldRecordOfMethod()
    {
        $this->attachmentField->expects($this->once())
            ->method('setAttachmentFieldRecordOf')
            ->with($this->formRecord, $this->formRecordData);
        $this->executeSetFieldRecordsOf();
    }
    public function test_setFieldRecordsOf_containRemovedAttachmentField_ignoreThisRemovedField()
    {
        $this->attachmentField->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->attachmentField->expects($this->never())
            ->method('setAttachmentFieldRecordOf');
        $this->executeSetFieldRecordsOf();
    }
    
    public function test_createFormRecord_returnFormRecord()
    {
        $this->assertInstanceOf(FormRecord::class, $this->form->createFormRecord($this->formRecordId, $this->formRecordData));
    }
    
}

class TestableForm extends Form
{
    public $id, $name, $description;
    public $stringFields, $integerFields, $textAreaFields, $singleSelectFields, $multiSelectFields, $attachmentFields;
    
    public function __construct()
    {
        parent::__construct();
    }

}
