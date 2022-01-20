<?php

namespace Firm\Domain\Model\Shared;

use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\AttachmentField;
use Firm\Domain\Model\Shared\Form\AttachmentFieldData;
use Firm\Domain\Model\Shared\Form\FieldData;
use Firm\Domain\Model\Shared\Form\IntegerField;
use Firm\Domain\Model\Shared\Form\IntegerFieldData;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use Firm\Domain\Model\Shared\Form\MultiSelectFieldData;
use Firm\Domain\Model\Shared\Form\Section;
use Firm\Domain\Model\Shared\Form\SectionData;
use Firm\Domain\Model\Shared\Form\SelectFieldData;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use Firm\Domain\Model\Shared\Form\SingleSelectFieldData;
use Firm\Domain\Model\Shared\Form\StringField;
use Firm\Domain\Model\Shared\Form\StringFieldData;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use Firm\Domain\Model\Shared\Form\TextAreaFieldData;
use Firm\Domain\Task\BioSearchFilterDataBuilder\BioFormSearchFilterRequest;
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
            $multiSelectFieldData,
            $sectionData;
    protected
            $stringFieldDataCollection,
            $integerFieldDataCollection,
            $textAreaFieldDataCollection,
            $attachmentFieldDataCollection,
            $singleSelectFieldDataCollection,
            $multiSelectFieldDataCollection,
            $sectionDataCollection;
    protected
            $stringField, $stringFieldId = 'stringFieldId',
            $integerField, $integerFieldId = 'integerFieldId',
            $textAreaField, $textAreaFieldId = 'textAreaFieldId',
            $attachmentField, $attachmentFieldId = 'attachmentFieldId',
            $singleSelectField, $singleSelectFieldId = 'singleSelectFieldId',
            $multiSelectField, $multiSelectFieldId = 'multiSelectFieldId',
            $section, $sectionId = 'sectionId';
    
    protected $bioSearchFilterData, $bioFormSearchFilterRequest, $comparisonType = 1;

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
        $this->section = $this->buildMockOfClass(Section::class);
        $this->section->expects($this->any())
                ->method('getId')
                ->willReturn($this->sectionId);

        $formData = new FormData('name', 'description');
        $this->form = new TestableForm('id', $formData);

        $this->form->stringFields->add($this->stringField);
        $this->form->integerFields->add($this->integerField);
        $this->form->textAreaFields->add($this->textAreaField);
        $this->form->singleSelectFields->add($this->singleSelectField);
        $this->form->multiSelectFields->add($this->multiSelectField);
        $this->form->attachmentFields->add($this->attachmentField);
        $this->form->sections->add($this->section);

        $fieldData = new FieldData('name', 'description', 'position', true);
        $selectFieldData = new SelectFieldData($fieldData);
        
        $this->stringFieldData = new StringFieldData($fieldData, null, null, '', '');
        $this->integerFieldData = new IntegerFieldData($fieldData, null, null, '', null);
        $this->textAreaFieldData = new TextAreaFieldData($fieldData, null, null, '', '');
        $this->singleSelectFieldData = new SingleSelectFieldData($selectFieldData, '');
        $this->multiSelectFieldData = new MultiSelectFieldData($selectFieldData, null, null);
        $this->attachmentFieldData = new AttachmentFieldData($fieldData, null, null);
        $this->sectionData = new SectionData('section name', 'section position');
        
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
        $this->sectionDataCollection = new DataCollection();
        $this->sectionDataCollection->push($this->sectionData, null);

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
        $this->formData->expects($this->any())
                ->method('getSectionDataCollection')
                ->willReturn($this->sectionDataCollection);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
        $this->bioFormSearchFilterRequest = new BioFormSearchFilterRequest('form-id');
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
    public function test_construct_containSectionData_addSectionToCollection()
    {
        $form = $this->executeConstruct();
        $this->assertEquals(1, $form->sections->count());
        $this->assertInstanceOf(Section::class, $form->sections->first());
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
                ->method('pullSectionDataOfId')
                ->with($this->sectionId)
                ->willReturn($this->sectionData);

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
    public function test_update_sectionInCollectionHasCorrespondingData_updateThisField()
    {
        $this->section->expects($this->once())
                ->method('update')
                ->with($this->sectionData);
        $this->executeUpdate();
    }
    public function test_update_sectionInCollectionAlreadyRemoved_ignoreThisField()
    {
        $this->section->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->section->expects($this->never())
                ->method('update');
        $this->executeUpdate();
    }
    public function test_update_sectionInCollectionHasNoCorrespondingData_removeThisField()
    {
        $this->formData->expects($this->once())
                ->method('pullSectionDataOfId')
                ->with($this->sectionId)
                ->willReturn(null);
        $this->section->expects($this->once())
                ->method('remove');
        $this->executeUpdate();
    }
    
    protected function executeSetFieldFiltersToBioSearchFilter()
    {
        $this->form->setFieldFiltersToBioSearchFilterData($this->bioSearchFilterData, $this->bioFormSearchFilterRequest);
    }
    public function test_setFieldFiltersToBioSearchFilter_hasIntegerFieldFilter_addIntegerFieldFilterToBioSearchFilterData()
    {
        $this->bioFormSearchFilterRequest->addIntegerFieldSearchFilterRequest($this->integerFieldId, $this->comparisonType);
        $this->form->integerFields->add($this->integerField);
        
        $this->bioSearchFilterData->expects($this->once())
                ->method('addIntegerFieldFilter')
                ->with($this->integerField, $this->comparisonType);
        $this->executeSetFieldFiltersToBioSearchFilter();
    }
    public function test_setFieldFiltersToBioSearchFilter_noIntegerFieldCorrespondWithFieldIdFound_forbidden()
    {
        $this->bioFormSearchFilterRequest->addIntegerFieldSearchFilterRequest('non-existing-field', $this->comparisonType);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSetFieldFiltersToBioSearchFilter();
        }, 'Not Found', 'not found: field not found');
    }
    public function test_setFieldFiltersToBioSearchFilter_hasStringFieldFilter_addStringFieldFilterToBioSearchFilterData()
    {
        $this->bioFormSearchFilterRequest->addStringFieldSearchFilterRequest($this->stringFieldId, $this->comparisonType);
        $this->form->stringFields->add($this->stringField);
        
        $this->bioSearchFilterData->expects($this->once())
                ->method('addStringFieldFilter')
                ->with($this->stringField, $this->comparisonType);
        $this->executeSetFieldFiltersToBioSearchFilter();
    }
    public function test_setFieldFiltersToBioSearchFilter_noStringFieldCorrespondWithFieldIdFound_forbidden()
    {
        $this->bioFormSearchFilterRequest->addStringFieldSearchFilterRequest('non-existing-field', $this->comparisonType);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSetFieldFiltersToBioSearchFilter();
        }, 'Not Found', 'not found: field not found');
    }
    public function test_setFieldFiltersToBioSearchFilter_hasTextAreaFieldFilter_addTextAreaFieldFilterToBioSearchFilterData()
    {
        $this->bioFormSearchFilterRequest->addTextAreaFieldSearchFilterRequest($this->textAreaFieldId, $this->comparisonType);
        $this->form->textAreaFields->add($this->textAreaField);
        
        $this->bioSearchFilterData->expects($this->once())
                ->method('addTextAreaFieldFilter')
                ->with($this->textAreaField, $this->comparisonType);
        $this->executeSetFieldFiltersToBioSearchFilter();
    }
    public function test_setFieldFiltersToBioSearchFilter_noTextAreaFieldCorrespondWithFieldIdFound_forbidden()
    {
        $this->bioFormSearchFilterRequest->addTextAreaFieldSearchFilterRequest('non-existing-field', $this->comparisonType);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSetFieldFiltersToBioSearchFilter();
        }, 'Not Found', 'not found: field not found');
    }
    public function test_setFieldFiltersToBioSearchFilter_hasSingleSelectFieldFilter_addSingleSelectFieldFilterToBioSearchFilterData()
    {
        $this->bioFormSearchFilterRequest->addSingleSelectFieldSearchFilterRequest($this->singleSelectFieldId, $this->comparisonType);
        $this->form->singleSelectFields->add($this->singleSelectField);
        
        $this->bioSearchFilterData->expects($this->once())
                ->method('addSingleSelectFieldFilter')
                ->with($this->singleSelectField, $this->comparisonType);
        $this->executeSetFieldFiltersToBioSearchFilter();
    }
    public function test_setFieldFiltersToBioSearchFilter_noSingleSelectFieldCorrespondWithFieldIdFound_forbidden()
    {
        $this->bioFormSearchFilterRequest->addSingleSelectFieldSearchFilterRequest('non-existing-field', $this->comparisonType);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSetFieldFiltersToBioSearchFilter();
        }, 'Not Found', 'not found: field not found');
    }
    public function test_setFieldFiltersToBioSearchFilter_hasMultiSelectFieldFilter_addMultiSelectFieldFilterToBioSearchFilterData()
    {
        $this->bioFormSearchFilterRequest->addMultiSelectFieldSearchFilterRequest($this->multiSelectFieldId, $this->comparisonType);
        $this->form->multiSelectFields->add($this->multiSelectField);
        
        $this->bioSearchFilterData->expects($this->once())
                ->method('addMultiSelectFieldFilter')
                ->with($this->multiSelectField, $this->comparisonType);
        $this->executeSetFieldFiltersToBioSearchFilter();
    }
    public function test_setFieldFiltersToBioSearchFilter_noMultiSelectFieldCorrespondWithFieldIdFound_forbidden()
    {
        $this->bioFormSearchFilterRequest->addMultiSelectFieldSearchFilterRequest('non-existing-field', $this->comparisonType);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeSetFieldFiltersToBioSearchFilter();
        }, 'Not Found', 'not found: field not found');
    }

}

class TestableForm extends Form
{

    public $id, $name, $description;
    public $stringFields, $integerFields, $textAreaFields, $singleSelectFields, $multiSelectFields, $attachmentFields, 
            $sections;

}
