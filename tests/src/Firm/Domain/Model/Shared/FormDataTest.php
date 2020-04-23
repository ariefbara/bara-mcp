<?php

namespace Firm\Domain\Model\Shared;

use Firm\Domain\Model\Shared\Form\ {
    AttachmentFieldData,
    IntegerFieldData,
    MultiSelectFieldData,
    SingleSelectFieldData,
    StringFieldData,
    TextAreaFieldData
};
use Resources\Domain\Data\DataCollection;
use Tests\TestBase;

class FormDataTest extends TestBase
{
    protected $formData;
    protected $collection; 
    protected $stringFieldData, $integerFieldData, $textAreaFieldData, $singleSelectFieldData, $multiSelectFieldData, 
            $attachmentFieldData;
    protected $fieldId = 'fieldId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = new TestableFormData('name', 'description');
        $this->collection = $this->buildMockOfClass(DataCollection::class);
        $this->formData->stringFieldDataCollection = $this->collection;
        $this->formData->integerFieldDataCollection = $this->collection;
        $this->formData->textAreaFieldDataCollection = $this->collection;
        $this->formData->singleSelectFieldDataCollection = $this->collection;
        $this->formData->multiSelectFieldDataCollection = $this->collection;
        $this->formData->attachmentFieldDataCollection= $this->collection;
        
        $this->stringFieldData = $this->buildMockOfClass(StringFieldData::class);
        $this->integerFieldData = $this->buildMockOfClass(IntegerFieldData::class);
        $this->textAreaFieldData = $this->buildMockOfClass(TextAreaFieldData::class);
        $this->singleSelectFieldData = $this->buildMockOfClass(SingleSelectFieldData::class);
        $this->multiSelectFieldData = $this->buildMockOfClass(MultiSelectFieldData::class);
        $this->attachmentFieldData = $this->buildMockOfClass(AttachmentFieldData::class);
    }
    public function test_construct_setProperties()
    {
        $formData = new TestableFormData('name', 'description');
        $this->assertEquals('name', $formData->name);
        $this->assertEquals('description', $formData->description);
        $this->assertInstanceOf(DataCollection::class, $formData->stringFieldDataCollection);
        $this->assertInstanceOf(DataCollection::class, $formData->integerFieldDataCollection);
        $this->assertInstanceOf(DataCollection::class, $formData->textAreaFieldDataCollection);
        $this->assertInstanceOf(DataCollection::class, $formData->singleSelectFieldDataCollection);
        $this->assertInstanceOf(DataCollection::class, $formData->multiSelectFieldDataCollection);
        $this->assertInstanceOf(DataCollection::class, $formData->attachmentFieldDataCollection);
    }
    
    public function test_pushStringFieldData_pushStringFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->stringFieldData, $this->fieldId);
        $this->formData->pushStringFieldData($this->stringFieldData, $this->fieldId);
    }
    public function test_pullStringFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->stringFieldData);
        $this->assertEquals($this->stringFieldData, $this->formData->pullStringFieldDataOfId($this->fieldId));
    }
    
    public function test_pushIntegerFieldData_pushIntegerFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->integerFieldData, $this->fieldId);
        $this->formData->pushIntegerFieldData($this->integerFieldData, $this->fieldId);
    }
    public function test_pullIntegerFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->integerFieldData);
        $this->assertEquals($this->integerFieldData, $this->formData->pullIntegerFieldDataOfId($this->fieldId));
    }
    
    public function test_pushTextAreaFieldData_pushTextAreaFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->textAreaFieldData, $this->fieldId);
        $this->formData->pushTextAreaFieldData($this->textAreaFieldData, $this->fieldId);
    }
    public function test_pullTextAreaFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->textAreaFieldData);
        $this->assertEquals($this->textAreaFieldData, $this->formData->pullTextAreaFieldDataOfId($this->fieldId));
    }
    
    public function test_pushSingleSelectFieldData_pushSingleSelectFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->singleSelectFieldData, $this->fieldId);
        $this->formData->pushSingleSelectFieldData($this->singleSelectFieldData, $this->fieldId);
    }
    public function test_pullSingleSelectFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->singleSelectFieldData);
        $this->assertEquals($this->singleSelectFieldData, $this->formData->pullSingleSelectFieldDataOfId($this->fieldId));
    }
    
    public function test_pushMultiSelectFieldData_pushMultiSelectFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->multiSelectFieldData, $this->fieldId);
        $this->formData->pushMultiSelectFieldData($this->multiSelectFieldData, $this->fieldId);
    }
    public function test_pullMultiSelectFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->multiSelectFieldData);
        $this->assertEquals($this->multiSelectFieldData, $this->formData->pullMultiSelectFieldDataOfId($this->fieldId));
    }
    
    public function test_pushAttachmentFieldData_pushAttachmentFieldDataToCollection()
    {
        $this->collection->expects($this->once())
                ->method('push')
                ->with($this->attachmentFieldData, $this->fieldId);
        $this->formData->pushAttachmentFieldData($this->attachmentFieldData, $this->fieldId);
    }
    public function test_pullAttachmentFieldDataOfId_returnDataFromCollectionPullMethod()
    {
        $this->collection->expects($this->once())
                ->method('pull')
                ->with($this->fieldId)
                ->willReturn($this->attachmentFieldData);
        $this->assertEquals($this->attachmentFieldData, $this->formData->pullAttachmentFieldDataOfId($this->fieldId));
    }
}

class TestableFormData extends FormData
{
    public $name, $description;
    public $stringFieldDataCollection, $integerFieldDataCollection, $textAreaFieldDataCollection, 
            $attachmentFieldDataCollection, $singleSelectFieldDataCollection, $multiSelectFieldDataCollection;
}
