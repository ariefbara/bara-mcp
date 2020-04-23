<?php

namespace Firm\Domain\Model\Shared;

use Firm\Domain\Model\Shared\Form\ {
    AttachmentField,
    AttachmentFieldData,
    FieldData,
    IntegerField,
    IntegerFieldData,
    MultiSelectField,
    MultiSelectFieldData,
    SelectFieldData,
    SingleSelectField,
    SingleSelectFieldData,
    StringField,
    StringFieldData,
    TextAreaField,
    TextAreaFieldData
};
use Resources\Domain\Data\DataCollection;
use Tests\TestBase;

class FormTest extends TestBase
{

    protected $form;
    protected $id = 'newId', $name = 'new name', $description = 'new description';
    protected $formData;
    protected
            $stringFieldData,
            $integerFieldData,
            $textAreaFieldData,
            $attachmentFieldData,
            $singleSelectFieldData,
            $multiSelectFieldData;
    protected
            $stringFieldDataCollection,
            $integerFieldDataCollection,
            $textAreaFieldDataCollection,
            $attachmentFieldDataCollection,
            $singleSelectFieldDataCollection,
            $multiSelectFieldDataCollection;
    protected
            $stringField, $stringFieldId = 'stringFieldId',
            $integerField, $integerFieldId = 'integerFieldId',
            $textAreaField, $textAreaFieldId = 'textAreaFieldId',
            $attachmentField, $attachmentFieldId = 'attachmentFieldId',
            $singleSelectField, $singleSelectFieldId = 'singleSelectFieldId',
            $multiSelectField, $multiSelectFieldId = 'multiSelectFieldId';

    protected function setUp(): void
    {
        parent::setUp();

        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->stringField->expects($this->any())
                ->method('getId')
                ->willReturn($this->stringFieldId);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->integerField->expects($this->any())
                ->method('getId')
                ->willReturn($this->integerFieldId);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->textAreaField->expects($this->any())
                ->method('getId')
                ->willReturn($this->textAreaFieldId);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->singleSelectField->expects($this->any())
                ->method('getId')
                ->willReturn($this->singleSelectFieldId);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        $this->multiSelectField->expects($this->any())
                ->method('getId')
                ->willReturn($this->multiSelectFieldId);
        $this->attachmentField = $this->buildMockOfClass(AttachmentField::class);
        $this->attachmentField->expects($this->any())
                ->method('getId')
                ->willReturn($this->attachmentFieldId);

        $formData = new FormData('name', 'description');
        $this->form = new TestableForm('id', $formData);

        $this->form->stringFields->add($this->stringField);
        $this->form->integerFields->add($this->integerField);
        $this->form->textAreaFields->add($this->textAreaField);
        $this->form->singleSelectFields->add($this->singleSelectField);
        $this->form->multiSelectFields->add($this->multiSelectField);
        $this->form->attachmentFields->add($this->attachmentField);

        $fieldData = new FieldData('name', 'description', 'position', true);
        $selectFieldData = new SelectFieldData($fieldData);
        
        $this->stringFieldData = new StringFieldData($fieldData, null, null, '', '');
        $this->integerFieldData = new IntegerFieldData($fieldData, null, null, '', null);
        $this->textAreaFieldData = new TextAreaFieldData($fieldData, null, null, '', '');
        $this->singleSelectFieldData = new SingleSelectFieldData($selectFieldData, '');
        $this->multiSelectFieldData = new MultiSelectFieldData($selectFieldData, null, null);
        $this->attachmentFieldData = new AttachmentFieldData($fieldData, null, null);
        
        $this->stringFieldDataCollection = new DataCollection();
        $this->stringFieldDataCollection->push($this->stringFieldData, null);
        $this->integerFieldDataCollection = new DataCollection();
        $this->integerFieldDataCollection->push($this->integerFieldData, null);
        $this->textAreaFieldDataCollection = new DataCollection();
        $this->textAreaFieldDataCollection->push($this->textAreaFieldData, null);
        $this->singleSelectFieldDataCollection = new DataCollection();
        $this->singleSelectFieldDataCollection->push($this->singleSelectFieldData, null);
        $this->multiSelectFieldDataCollection = new DataCollection();
        $this->multiSelectFieldDataCollection->push($this->multiSelectFieldData, null);
        $this->attachmentFieldDataCollection = new DataCollection();
        $this->attachmentFieldDataCollection->push($this->attachmentFieldData, null);

        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getStringFieldDataCollection')
                ->willReturn($this->stringFieldDataCollection);
        $this->formData->expects($this->any())
                ->method('getIntegerFieldDataCollection')
                ->willReturn($this->integerFieldDataCollection);
        $this->formData->expects($this->any())
                ->method('getTextAreaFieldDataCollection')
                ->willReturn($this->textAreaFieldDataCollection);
        $this->formData->expects($this->any())
                ->method('getSingleSelectFieldDataCollection')
                ->willReturn($this->singleSelectFieldDataCollection);
        $this->formData->expects($this->any())
                ->method('getMultiSelectFieldDataCollection')
                ->willReturn($this->multiSelectFieldDataCollection);
        $this->formData->expects($this->any())
                ->method('getAttachmentFieldDataCollection')
                ->willReturn($this->attachmentFieldDataCollection);
    }

    protected function executeConstruct()
    {
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn($this->name);
        $this->formData->expects($this->any())
                ->method('getDescription')
                ->willReturn($this->description);

        return new TestableForm($this->id, $this->formData);
    }

    public function test_construct_setProperties()
    {
        $form = $this->executeConstruct();
        $this->assertEquals($this->id, $form->id);
        $this->assertEquals($this->name, $form->name);
        $this->assertEquals($this->description, $form->description);
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = ' ';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: form name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_containsStirngFieldData_addStringFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->stringFields->count());
        $this->assertInstanceOf(StringField::class, $form->stringFields->first());
    }

    public function test_construct_containIntegerFieldData_addIntegerFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->integerFields->count());
        $this->assertInstanceOf(IntegerField::class, $form->integerFields->first());
    }

    public function test_construct_containTextAreaFieldData_addTextAreaFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->textAreaFields->count());
        $this->assertInstanceOf(TextAreaField::class, $form->textAreaFields->first());
    }

    public function test_construct_containSingleSelectFieldData_addSingleSelectFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->singleSelectFields->count());
        $this->assertInstanceOf(SingleSelectField::class, $form->singleSelectFields->first());
    }

    public function test_construct_containMultiSelectFieldData_addMultiSelectFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->multiSelectFields->count());
        $this->assertInstanceOf(MultiSelectField::class, $form->multiSelectFields->first());
    }

    public function test_construct_containAttachmentFieldData_addAttachmentFieldToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->attachmentFields->count());
        $this->assertInstanceOf(AttachmentField::class, $form->attachmentFields->first());
    }

    protected function executeUpdate()
    {
        $this->formData->expects($this->any())
                ->method('pullStringFieldDataOfId')
                ->with($this->stringFieldId)
                ->willReturn($this->stringFieldData);
        $this->formData->expects($this->any())
                ->method('pullIntegerFieldDataOfId')
                ->with($this->integerFieldId)
                ->willReturn($this->integerFieldData);
        $this->formData->expects($this->any())
                ->method('pullTextAreaFieldDataOfId')
                ->with($this->textAreaFieldId)
                ->willReturn($this->textAreaFieldData);
        $this->formData->expects($this->any())
                ->method('pullSingleSelectFieldDataOfId')
                ->with($this->singleSelectFieldId)
                ->willReturn($this->singleSelectFieldData);
        $this->formData->expects($this->any())
                ->method('pullMultiSelectFieldDataOfId')
                ->with($this->multiSelectFieldId)
                ->willReturn($this->multiSelectFieldData);
        $this->formData->expects($this->any())
                ->method('pullAttachmentFieldDataOfId')
                ->with($this->attachmentFieldId)
                ->willReturn($this->attachmentFieldData);

        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn($this->name);
        $this->formData->expects($this->any())
                ->method('getDescription')
                ->willReturn($this->description);

        $this->form->update($this->formData);
    }

    public function test_update_updatePropertiesAndAddFieldsToCollection()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->form->name);
        $this->assertEquals($this->description, $this->form->description);
        $this->assertEquals(2, $this->form->stringFields->count());
        $this->assertEquals(2, $this->form->integerFields->count());
        $this->assertEquals(2, $this->form->textAreaFields->count());
        $this->assertEquals(2, $this->form->singleSelectFields->count());
        $this->assertEquals(2, $this->form->multiSelectFields->count());
        $this->assertEquals(2, $this->form->attachmentFields->count());
    }

    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeUpdate();
        };
        $errorDetail = "bad request: form name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_update_stringFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->stringField->expects($this->once())
                ->method('update')
                ->with($this->stringFieldData);
        $this->executeUpdate();
    }

    public function test_update_stringFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->stringField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->stringField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_stringFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullStringFieldDataOfId')
                ->with($this->stringFieldId)
                ->willReturn(null);
        $this->stringField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_integerFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->integerField->expects($this->once())
                ->method('update')
                ->with($this->integerFieldData);
        $this->executeUpdate();
    }

    public function test_update_integerFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->integerField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->integerField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_integerFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullIntegerFieldDataOfId')
                ->with($this->integerFieldId)
                ->willReturn(null);
        $this->integerField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_textAreaFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->textAreaField->expects($this->once())
                ->method('update')
                ->with($this->textAreaFieldData);
        $this->executeUpdate();
    }

    public function test_update_textAreaFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->textAreaField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->textAreaField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_textAreaFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullTextAreaFieldDataOfId')
                ->with($this->textAreaFieldId)
                ->willReturn(null);
        $this->textAreaField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_singleSelectFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->singleSelectField->expects($this->once())
                ->method('update')
                ->with($this->singleSelectFieldData);
        $this->executeUpdate();
    }

    public function test_update_singleSelectFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->singleSelectField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->singleSelectField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_singleSelectFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullSingleSelectFieldDataOfId')
                ->with($this->singleSelectFieldId)
                ->willReturn(null);
        $this->singleSelectField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_multiSelectFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->multiSelectField->expects($this->once())
                ->method('update')
                ->with($this->multiSelectFieldData);
        $this->executeUpdate();
    }

    public function test_update_multiSelectFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->multiSelectField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->multiSelectField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_multiSelectFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullMultiSelectFieldDataOfId')
                ->with($this->multiSelectFieldId)
                ->willReturn(null);
        $this->multiSelectField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

    public function test_update_attachmentFieldInCollectionHasCorrespondingData_updateThisField()
    {
        $this->attachmentField->expects($this->once())
                ->method('update')
                ->with($this->attachmentFieldData);
        $this->executeUpdate();
    }

    public function test_update_attachmentFieldInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->attachmentField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->attachmentField->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }

    public function test_update_attachmentFieldInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullAttachmentFieldDataOfId')
                ->with($this->attachmentFieldId)
                ->willReturn(null);
        $this->attachmentField->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }

}

class TestableForm extends Form
{

    public $id, $name, $description;
    public $stringFields, $integerFields, $textAreaFields, $singleSelectFields, $multiSelectFields, $attachmentFields;

}
