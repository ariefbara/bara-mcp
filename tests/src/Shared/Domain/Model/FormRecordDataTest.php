<?php

namespace Shared\Domain\Model;

use Shared\Domain\Model\FormRecordData\ {
    AttachmentFieldRecordData,
    MultiSelectFieldRecordData
};
use Tests\TestBase;

class FormRecordDataTest extends TestBase
{
    protected $assignmentFormRecordData;
    protected $stringFieldId = 'string-FieldId', $stringData = 'string data';
    protected $integerFieldId = 'integer-FieldId', $integerData = 312;
    protected $textAreaFieldId = 'textArea-FieldId', $textAreaData = 'textArea data';
    protected $singleSelectFieldId = 'singleSelect-FieldId', $selectedOptionId = 'selectedOptionId';
    protected $multiSelectFieldId = 'multiSelect-FieldId', $multiSelectFieldRecordData;
    protected $attachmentFieldId = 'attachment-FieldId', $attachmentFieldRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->multiSelectFieldRecordData = $this->buildMockOfClass(MultiSelectFieldRecordData::class);
        $this->attachmentFieldRecordData = $this->buildMockOfClass(AttachmentFieldRecordData::class);
        
        $this->assignmentFormRecordData = new TestableFormRecordData();
        
        $this->assignmentFormRecordData->stringFieldRecordDatas[$this->stringFieldId] = $this->stringData;
        $this->assignmentFormRecordData->integerFieldRecordDatas[$this->integerFieldId] = $this->integerData;
        $this->assignmentFormRecordData->textAreaFieldRecordDatas[$this->textAreaFieldId] = $this->textAreaData;
        $this->assignmentFormRecordData->singleSelectFieldRecordDatas[$this->singleSelectFieldId] = $this->selectedOptionId;
        $this->assignmentFormRecordData->multiSelectFieldRecordDatas[$this->multiSelectFieldId] = $this->multiSelectFieldRecordData;
        $this->assignmentFormRecordData->attachmentFieldRecordDatas[$this->attachmentFieldId] = $this->attachmentFieldRecordData;
    }
    
    public function test_addStringFieldRecordData_addDataToStringCollection()
    {
        $this->assignmentFormRecordData->addStringFieldRecordData($fieldId = 'newFieldId', $data = 'new data');
        $collection = $this->assignmentFormRecordData->stringFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->stringFieldRecordDatas);
    }
    public function test_addIntegerFieldRecordData_addDataToIntegerCollection()
    {
        $this->assignmentFormRecordData->addIntegerFieldRecordData($fieldId = 'newFieldId', $data = 123);
        $collection = $this->assignmentFormRecordData->integerFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->integerFieldRecordDatas);
    }
    public function test_addTextAreaFieldRecordData_addDataToTextAreaCollection()
    {
        $this->assignmentFormRecordData->addTextAreaFieldRecordData($fieldId = 'newFieldId', $data = 'new data');
        $collection = $this->assignmentFormRecordData->textAreaFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->textAreaFieldRecordDatas);
    }
    public function test_addSingleSelectFieldRecordData_addDataToSingleSelectCollection()
    {
        $this->assignmentFormRecordData->addSingleSelectFieldRecordData($fieldId = 'newFieldId', $data = 'new data');
        $collection = $this->assignmentFormRecordData->singleSelectFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->singleSelectFieldRecordDatas);
    }
    public function test_addMultiSelectFieldRecordData_addDataToMultiSelectCollection()
    {
        $data = $this->buildMockOfClass(MultiSelectFieldRecordData::class);
        $data->expects($this->once())
                ->method('getMultiSelectFieldId')
                ->willReturn($fieldId = 'newFieldId');
        $this->assignmentFormRecordData->addMultiSelectFieldRecordData($data);
        
        $collection = $this->assignmentFormRecordData->multiSelectFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->multiSelectFieldRecordDatas);
    }
    public function test_addAttachmentFieldRecordData_addDataToAttachmentCollection()
    {
        $data = $this->buildMockOfClass(AttachmentFieldRecordData::class);
        $data->expects($this->once())
                ->method('getAttachmentFieldId')
                ->willReturn($fieldId = 'newFieldId');
        $this->assignmentFormRecordData->addAttachmentFieldRecordData($data);
        
        $collection = $this->assignmentFormRecordData->attachmentFieldRecordDatas;
        $collection[$fieldId] = $data;
        $this->assertEquals($collection, $this->assignmentFormRecordData->attachmentFieldRecordDatas);
    }
    
    public function test_getStringFieldRecordDataOf_returnStringData()
    {
        $this->assertEquals($this->stringData, $this->assignmentFormRecordData->getStringFieldRecordDataOf($this->stringFieldId));
    }
    public function test_getStringFieldRecordDataOf_collectionHasNoKeyEqualsToStringFieldId_returnNull()
    {
        $this->assertNull($this->assignmentFormRecordData->getStringFieldRecordDataOf('nonExistingFieldId'));
    }
    
    public function test_getIntegerFieldRecordDataOf_returnIntegerData()
    {
        $this->assertEquals($this->integerData, $this->assignmentFormRecordData->getIntegerFieldRecordDataOf($this->integerFieldId));
    }
    public function test_getIntegerFieldRecordDataOf_collectionHasNoKeyEqualsToIntegerFieldId_returnNull()
    {
        $this->assertNull($this->assignmentFormRecordData->getIntegerFieldRecordDataOf('nonExistingFieldId'));
    }
    
    public function test_getTextAreaFieldRecordDataOf_returnTextAreaData()
    {
        $this->assertEquals($this->textAreaData, $this->assignmentFormRecordData->getTextAreaFieldRecordDataOf($this->textAreaFieldId));
    }
    public function test_getTextAreaFieldRecordDataOf_noTextAreaIdInCollectionKey_returnNull()
    {
        $this->assertNull($this->assignmentFormRecordData->getTextAreaFieldRecordDataOf('nonExistingKey'));
    }
    
    public function test_getSelectedOptionIdOf_getSelectedOptionId()
    {
        $this->assertEquals($this->selectedOptionId, $this->assignmentFormRecordData->getSelectedOptionIdOf($this->singleSelectFieldId));
    }
    public function test_getSelectedOptionIdOf_noSingleSelectFieldIdInCollection_returnNull()
    {
        $this->assertNull($this->assignmentFormRecordData->getSelectedOptionIdOf('not exist'));
    }
    
    public function test_getSelectedOptionIdListOf_returnResultOfMultiSelectFieldRecordDatasGetSelectedOptionIdsMethod()
    {
        $this->multiSelectFieldRecordData->expects($this->once())
                ->method('getSelectedOptionIds')
                ->willReturn($result = ['selectedOptionId']);
        $this->assertEquals($result, $this->assignmentFormRecordData->getSelectedOptionIdListOf($this->multiSelectFieldId));
    }
    public function test_getSelectedOptionListOf_noKeyInCollection_returnEmptyArray()
    {
        $this->assertEquals([], $this->assignmentFormRecordData->getSelectedOptionIdListOf('nonExist'));
    }
    
    public function test_getAttachedFileInfoListOf_returnAttachmentFieldRecordDatasGetFileInfoStorageMethod()
    {
        $result = [$this->buildMockOfClass(FileInfo::class)];
        $this->attachmentFieldRecordData->expects($this->once())
                ->method('getFileInfoCollection')
                ->willReturn($result);
        $this->assertEquals($result, $this->assignmentFormRecordData->getAttachedFileInfoListOf($this->attachmentFieldId));
    }
    public function test_getAttachedFileInfoListOf_noKeyExist_returnEmptySplObjectStorage()
    {
        $this->assertEquals([], $this->assignmentFormRecordData->getAttachedFileInfoListOf('notExist'));
    }

}

class TestableFormRecordData extends FormRecordData
{
    public $stringFieldRecordDatas;
    public $integerFieldRecordDatas;
    public $textAreaFieldRecordDatas;
    public $attachmentFieldRecordDatas;
    public $singleSelectFieldRecordDatas;
    public $multiSelectFieldRecordDatas;
}
