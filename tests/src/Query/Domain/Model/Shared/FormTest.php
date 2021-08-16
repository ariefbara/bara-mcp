<?php

namespace Query\Domain\Model\Shared;

use Doctrine\Common\Collections\ArrayCollection;
use Tests\TestBase;

class FormTest extends TestBase
{
    protected $form;
    protected $integerField, $integerFieldName = 'integer field label', $integerRecordValue = 999;
    protected $stringField, $stringFieldName = 'string field label', $stringRecordValue = 'string record value';
    protected $textAreaField, $textAreaFieldName = 'text area field label', $textAreaRecordValue = 'text area record value';
    protected $attachmentField, $attachmentFieldName = 'attachment field label', $attachmentRecordValue = 'attachment record value';
    protected $singleSelectField, $singleSelectFieldName = 'single select field label', $singleSelectRecordValue = 'single select field record value';
    protected $multiSelectField, $multiSelectFieldName = 'multi select field label', $multiSelectRecordValue = 'multi select field record value';
    protected $formRecord;
            
    function setUp(): void
    {
        parent::setUp();
        $this->form = new TestableForm();
        $this->form->integerFields = new ArrayCollection();
        $this->form->stringFields = new ArrayCollection();
        $this->form->textAreaFields = new ArrayCollection();
        $this->form->attachmentFields = new ArrayCollection();
        $this->form->singleSelectFields = new ArrayCollection();
        $this->form->multiSelectFields = new ArrayCollection();
        
        $this->integerField = $this->buildMockOfClass(Form\IntegerField::class);
        $this->integerField->expects($this->any())->method('getPosition')->willReturn('2');
        $this->integerField->expects($this->any())->method('getName')->willReturn($this->integerFieldName);
        
        $this->stringField = $this->buildMockOfClass(Form\StringField::class);
        $this->stringField->expects($this->any())->method('getPosition')->willReturn('4');
        $this->stringField->expects($this->any())->method('getName')->willReturn($this->stringFieldName);
        
        $this->textAreaField = $this->buildMockOfClass(Form\TextAreaField::class);
        $this->textAreaField->expects($this->any())->method('getPosition')->willReturn('1');
        $this->textAreaField->expects($this->any())->method('getName')->willReturn($this->textAreaFieldName);
        
        $this->attachmentField = $this->buildMockOfClass(Form\AttachmentField::class);
        $this->attachmentField->expects($this->any())->method('getPosition')->willReturn('6');
        $this->attachmentField->expects($this->any())->method('getName')->willReturn($this->attachmentFieldName);
        
        $this->singleSelectField = $this->buildMockOfClass(Form\SingleSelectField::class);
        $this->singleSelectField->expects($this->any())->method('getPosition')->willReturn('5');
        $this->singleSelectField->expects($this->any())->method('getName')->willReturn($this->singleSelectFieldName);
        
        $this->multiSelectField = $this->buildMockOfClass(Form\MultiSelectField::class);
        $this->multiSelectField->expects($this->any())->method('getPosition')->willReturn('3');
        $this->multiSelectField->expects($this->any())->method('getName')->willReturn($this->multiSelectFieldName);
        
        $this->form->integerFields->add($this->integerField);
        $this->form->stringFields->add($this->stringField);
        $this->form->textAreaFields->add($this->textAreaField);
        $this->form->attachmentFields->add($this->attachmentField);
        $this->form->singleSelectFields->add($this->singleSelectField);
        $this->form->multiSelectFields->add($this->multiSelectField);
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_toArrayOfSummaryTableHeader_returnFieldsLabelOrderedByPosition()
    {
        $summaryTableHeader = [
            $this->textAreaFieldName,
            $this->integerFieldName,
            $this->multiSelectFieldName,
            $this->stringFieldName,
            $this->singleSelectFieldName,
            $this->attachmentFieldName,
        ];
        $this->assertEquals($summaryTableHeader, $this->form->toArrayOfSummaryTableHeader());
    }
    
    protected function generateSummaryTableEntryFromRecord()
    {
        $this->integerField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->integerRecordValue);
        $this->stringField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->stringRecordValue);
        $this->textAreaField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->textAreaRecordValue);
        $this->attachmentField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->attachmentRecordValue);
        $this->singleSelectField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->singleSelectRecordValue);
        $this->multiSelectField->expects($this->any())->method('extractCorrespondingValueFromRecord')
                ->with($this->formRecord)->willReturn($this->multiSelectRecordValue);
        
        return $this->form->generateSummaryTableEntryFromRecord($this->formRecord);
    }
    public function test_generateSummaryTableEntryFromRecord_returnFormRecordValueOrderedAccordingToFieldsPosition()
    {
        $summaryTableEntry = [
            $this->textAreaRecordValue,
            $this->integerRecordValue,
            $this->multiSelectRecordValue,
            $this->stringRecordValue,
            $this->singleSelectRecordValue,
            $this->attachmentRecordValue,
        ];
        $this->assertEquals($summaryTableEntry, $this->generateSummaryTableEntryFromRecord());
    }
}

class TestableForm extends Form
{
    public $id = 'form-id';
    public $name = 'form name';
    public $description = 'form description';
    public $stringFields;
    public $integerFields;
    public $textAreaFields;
    public $attachmentFields;
    public $singleSelectFields;
    public $multiSelectFields;
    public $sortedFields;
    
    function __construct()
    {
    }
}
