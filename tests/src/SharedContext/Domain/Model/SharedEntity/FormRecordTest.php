<?php

namespace SharedContext\Domain\Model\SharedEntity;

use SharedContext\Domain\Model\SharedEntity\ {
    Form\AttachmentField,
    Form\IntegerField,
    Form\MultiSelectField,
    Form\SelectField\Option,
    Form\SingleSelectField,
    Form\StringField,
    Form\TextAreaField,
    FormRecord\AttachmentFieldRecord,
    FormRecord\IntegerFieldRecord,
    FormRecord\MultiSelectFieldRecord,
    FormRecord\SingleSelectFieldRecord,
    FormRecord\StringFieldRecord,
    FormRecord\TextAreaFieldRecord
};
use Tests\TestBase;

class FormRecordTest extends TestBase
{
    protected $form;
    protected $formRecord;
    protected $id = 'id', $formRecordData;
    protected $stringFieldRecord, $integerFieldRecord, $textAreaFieldRecord, $singleSelectFieldRecord,
            $multiSelectFieldRecord, $attachmentFieldRecord;
    protected $stringField, $integerField, $textAreaField, $singleSelectField, $multiSelectField, $attachmentField;
    protected $stringData = 'string input', $integerData = 999, $textAreaData = 'text area input',
            $selectedOption, $fileInfo;
    protected $fileInfoList;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecord = new TestableFormRecord($this->form, 'id', $this->formRecordData);

        $this->stringFieldRecord = $this->buildMockOfClass(StringFieldRecord::class);
        $this->integerFieldRecord = $this->buildMockOfClass(IntegerFieldRecord::class);
        $this->textAreaFieldRecord = $this->buildMockOfClass(TextAreaFieldRecord::class);
        $this->singleSelectFieldRecord = $this->buildMockOfClass(SingleSelectFieldRecord::class);
        $this->multiSelectFieldRecord = $this->buildMockOfClass(MultiSelectFieldRecord::class);
        $this->attachmentFieldRecord = $this->buildMockOfClass(AttachmentFieldRecord::class);

        $this->formRecord->stringFieldRecords->add($this->stringFieldRecord);
        $this->formRecord->integerFieldRecords->add($this->integerFieldRecord);
        $this->formRecord->textAreaFieldRecords->add($this->textAreaFieldRecord);
        $this->formRecord->singleSelectFieldRecords->add($this->singleSelectFieldRecord);
        $this->formRecord->multiSelectFieldRecords->add($this->multiSelectFieldRecord);
        $this->formRecord->attachmentFieldRecords->add($this->attachmentFieldRecord);

        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        $this->attachmentField = $this->buildMockOfClass(AttachmentField::class);

        $this->selectedOption = $this->buildMockOfClass(Option::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoList = [$this->fileInfo];
    }

    protected function executeConstruct()
    {
        return new TestableFormRecord($this->form, $this->id, $this->formRecordData);
    }

    function test_construct_setProperties()
    {
        $formRecord = $this->executeConstruct();
        $this->assertEquals($this->form, $formRecord->form);
        $this->assertEquals($this->id, $formRecord->id);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $formRecord->submitTime->format('Y-m-d H:i:s'));
    }

    public function test_construct_executeFormsSeecordsOfMethod()
    {
        $this->form->expects($this->once())
                ->method('setFieldRecordsof')
                ->with($this->anything(), $this->formRecordData);
        $this->executeConstruct();
    }

    protected function executeUpdate()
    {
        $this->formRecord->update($this->formRecordData);
    }

    public function test_update_executeFormsSetFieldRecordsOfMethod()
    {
        $this->form->expects($this->once())
                ->method('setFieldRecordsof')
                ->with($this->formRecord, $this->formRecordData);
        $this->executeUpdate();
    }

    public function test_update_containStringFieldRecordReferedToRemovedStringField_removeStringFieldRecord()
    {
        $this->stringFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->stringFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_stringFieldRecordReferedToRemovedStringAlreadyRemoved_ignoreRemovingThisRecord()
    {
        $this->stringFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->stringFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->stringFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_containIntegerFieldRecordReferedToRemovedIntegerField_removeIntegerFieldRecord()
    {
        $this->integerFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->integerFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_integerFieldRecordReferedToRemovedFieldAlreadyRemoved_ignoreRemovingThisRecord()
    {
        $this->integerFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->integerFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->integerFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_containTextAreaFieldRecordReferedToRemovedField_removeTextAreaFieldRecord()
    {
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->textAreaFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_textAreaFieldRecordReferedToRemovedFieldAlreadyRemove_ignoreRemovingThisRecord()
    {
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->textAreaFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_containSingleSelectFieldRecordReferedToRemovedField_removedThisRecord()
    {
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_singleSelectFieldRecordReferToRemovedFieldAlreadyRemoved_ignoreRemovingThisRecord()
    {
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->singleSelectFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_containMultiSelectFieldRecordReferToRemovedField_removeThisRecord()
    {
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_multiSelectFieldRecordReferToRemovedFieldAlreadyRemoved_ignoreRemovingThisRecord()
    {
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->multiSelectFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_containAttachmentFieldRecordReferToRemovedField_removeThisRecord()
    {
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->attachmentFieldRecord->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_attachmentFieldRecordReferToRemovedFieldAlreadyRemoved_ignoreRemovingThisRecord()
    {
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isReferToRemovedField')
                ->willReturn(true);
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->attachmentFieldRecord->expects($this->never())
                ->method('remove');
        $this->executeUpdate();
    }

    protected function executeSetStringFieldRecord()
    {
        $this->formRecord->setStringFieldRecord($this->stringField, $this->stringData);
    }

    public function test_setStringField_addStringFieldRecordToCollection()
    {
        $this->executeSetStringFieldRecord();
        $this->assertEquals(2, $this->formRecord->stringFieldRecords->count());
        $this->assertInstanceOf(StringFieldRecord::class, $this->formRecord->stringFieldRecords->last());
    }

    function test_setStringFieldRecord_aStringFieldRecordReferToSameFieldAlreadyExistInCollection_udpateThisRecordInstead()
    {
        $this->stringFieldRecord->expects($this->once())
                ->method('getStringField')
                ->willReturn($this->stringField);
        $this->stringFieldRecord->expects($this->once())
                ->method('update')
                ->with($this->stringData);

        $this->executeSetStringFieldRecord();
    }

    function test_setStringFieldRecord_existingStringFieldRecordReferToSameFieldAlreadyRemoved_addNewRecordToCollection()
    {
        $this->stringFieldRecord->expects($this->once())
                ->method('getStringField')
                ->willReturn($this->stringField);
        $this->stringFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->stringFieldRecord->expects($this->never())
                ->method('update');

        $this->executeSetStringFieldRecord();
    }

    private function executeSetIntegerFieldRecord()
    {
        $this->formRecord->setIntegerFieldRecord($this->integerField, $this->integerData);
    }

    function test_setIntegerFieldRecord_addIntegerFieldRecordToCollection()
    {
        $this->executeSetIntegerFieldRecord();
        $this->assertEquals(2, $this->formRecord->integerFieldRecords->count());
        $this->assertInstanceOf(IntegerFieldRecord::class, $this->formRecord->integerFieldRecords->last());
    }

    function test_setIntegerFieldRecord_alreadyContainBusinessCanvasIntegerFieldRecordReferToSameField_updateValueOfExistingBusinessCanvasIntegerFieldRecordInstead()
    {
        $this->integerFieldRecord->expects($this->once())
                ->method('getIntegerField')
                ->willReturn($this->integerField);
        $this->integerFieldRecord->expects($this->once())
                ->method('update')
                ->with($this->integerData);

        $this->executeSetIntegerFieldRecord();
    }

    function test_setIntegerFieldRecord_existingIntegerFieldRecordReferToSameFieldAlreadyRemoved_addNewIntegerFieldRecordToCollection()
    {
        $this->integerFieldRecord->expects($this->once())
                ->method('getIntegerField')
                ->willReturn($this->integerField);
        $this->integerFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->integerFieldRecord->expects($this->never())
                ->method('update');

        $this->executeSetIntegerFieldRecord();
    }

    protected function executeSetTextAreaFieldRecord()
    {
        $this->formRecord->setTextAreaFieldRecord($this->textAreaField, $this->textAreaData);
    }

    function test_setTextAreaFieldRecord_addTextAreaFieldRecordToCollection()
    {
        $this->executeSetTextAreaFieldRecord();
        $this->assertEquals(2, $this->formRecord->textAreaFieldRecords->count());
        $this->assertInstanceOf(TextAreaFieldRecord::class, $this->formRecord->textAreaFieldRecords->last());
    }

    function test_setTextAreaFieldRecord_aTextAreaFieldRecordReferToSameFieldAlreadyExist_updateThisRecordInstead()
    {
        $this->textAreaFieldRecord->expects($this->once())
                ->method('getTextAreaField')
                ->willReturn($this->textAreaField);
        $this->textAreaFieldRecord->expects($this->once())
                ->method('update')
                ->with($this->textAreaData);

        $this->executeSetTextAreaFieldRecord();
    }

    function test_setTextAreaFieldRecord_textAreaFieldRecordReferToSameFieldAlreadyRemoved_addNewRecord()
    {
        $this->textAreaFieldRecord->expects($this->once())
                ->method('getTextAreaField')
                ->willReturn($this->textAreaField);
        $this->textAreaFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->textAreaFieldRecord->expects($this->never())
                ->method('update');

        $this->executeSetTextAreaFieldRecord();
        $this->assertEquals(2, $this->formRecord->textAreaFieldRecords->count());
    }

    protected function executeSetSingleSelectFieldRecord()
    {
        $this->formRecord->setSingleSelectFieldRecord($this->singleSelectField, $this->selectedOption);
    }

    public function test_setSingleSelectFieldRecord_addSingleSelectFieldRecordToCollection()
    {
        $this->executeSetSingleSelectFieldRecord();
        $this->assertEquals(2, $this->formRecord->singleSelectFieldRecords->count());
        $this->assertInstanceOf(SingleSelectFieldRecord::class, $this->formRecord->singleSelectFieldRecords->last());
    }

    public function test_setSingleSelectFieldRecord_containSingleSelectFieldRecordReferToSameField_updateThisRecordInstead()
    {
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('getSingleSelectField')
                ->willReturn($this->singleSelectField);
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('update')
                ->with($this->selectedOption);
        $this->executeSetSingleSelectFieldRecord();
    }

    public function test_setSingleSelectFieldRecord_recordReferToSameFieldAlreadyRemoved_ignoreUpdateRecordAndAddNewRecordInstead()
    {
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('getSingleSelectField')
                ->willReturn($this->singleSelectField);
        $this->singleSelectFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->singleSelectFieldRecord->expects($this->never())
                ->method('update');

        $this->executeSetSingleSelectFieldRecord();
        $this->assertEquals(2, $this->formRecord->singleSelectFieldRecords->count());
    }

    public function test_setSingleSelectFieldRecord_emptySelectedOption_processNormally()
    {
        $this->selectedOption = null;
        $this->executeSetSingleSelectFieldRecord();
        $this->assertEquals(2, $this->formRecord->singleSelectFieldRecords->count());
    }

    protected function executeSetMultiSelectFieldRecord()
    {
        $this->formRecord->setMultiSelectFieldRecord($this->multiSelectField, [$this->selectedOption]);
    }

    public function test_setMultiSelectFieldRecord_addMultiSelectFieldToCollection()
    {
        $this->executeSetMultiSelectFieldRecord();
        $this->assertEquals(2, $this->formRecord->multiSelectFieldRecords->count());
        $this->assertInstanceOf(MultiSelectFieldRecord::class, $this->formRecord->multiSelectFieldRecords->last());
    }

    public function test_setMultiSelectFieldRecord_containRecordReferToSameField_updateThisRecordInstead()
    {
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('getMultiSelectField')
                ->willReturn($this->multiSelectField);
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('setSelectedOptions')
                ->with([$this->selectedOption]);
        $this->executeSetMultiSelectFieldRecord();
    }

    public function test_setMultiSelectFieldRecord_recordReferToSameFieldAlreadyRemoved_addNewRecordInsteadOfUpdating()
    {
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('getMultiSelectField')
                ->willReturn($this->multiSelectField);
        $this->multiSelectFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->multiSelectFieldRecord->expects($this->never())
                ->method('setSelectedOptions');
        $this->executeSetMultiSelectFieldRecord();
        $this->assertEquals(2, $this->formRecord->multiSelectFieldRecords->count());
    }

    protected function executeSetAttachmentFieldRecord()
    {
        $this->formRecord->setAttachmentFieldRecord($this->attachmentField, $this->fileInfoList);
    }

    function test_setAttachmentFieldRecord_addAttachmentFieldRecordToCollection()
    {
        $this->executeSetAttachmentFieldRecord();
        $this->assertEquals(2, $this->formRecord->attachmentFieldRecords->count());
        $this->assertInstanceOf(AttachmentFieldRecord::class, $this->formRecord->attachmentFieldRecords->last());
    }

    function test_setAttachmentFieldRecord_anAttachmentFieldRecordReferToSameAttachmentFieldAlreadyExist_updateThisRecordInstead()
    {
        $this->attachmentFieldRecord->expects($this->once())
                ->method('getAttachmentField')
                ->willReturn($this->attachmentField);
        $this->attachmentFieldRecord->expects($this->once())
                ->method('setAttachedFiles')
                ->with($this->fileInfoList);
        $this->executeSetAttachmentFieldRecord();
    }

    function test_setAttachmentFieldRecord_existingAttachmentFieldRecordReferToSameFieldAlreadyRemoved_addNewRecord()
    {
        $this->attachmentFieldRecord->expects($this->once())
                ->method('getAttachmentField')
                ->willReturn($this->attachmentField);
        $this->attachmentFieldRecord->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->attachmentFieldRecord->expects($this->never())
                ->method('setAttachedFiles');

        $this->executeSetAttachmentFieldRecord();
        $this->assertEquals(2, $this->formRecord->attachmentFieldRecords->count());
    }

}

class TestableFormRecord extends FormRecord
{
    public $form;
    public $id, $submitTime, $removed;
    public $integerFieldRecords, $stringFieldRecords, $textAreaFieldRecords, $singleSelectFieldRecords,
            $attachmentFieldRecords, $multiSelectFieldRecords;

}
