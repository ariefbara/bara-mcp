<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    Form\AttachmentField,
    FormRecord,
    FormRecord\AttachmentFieldRecord\AttachedFile
};
use Tests\TestBase;

class AttachmentFieldRecordTest extends TestBase
{
    protected $formRecord, $field;
    protected $attachmentFieldRecord;
    protected $attachedFile;
    protected $id = 'attachment field record id', $fileInfo, $fileInfoList;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->field = $this->buildMockOfClass(AttachmentField::class);
        
        $this->attachmentFieldRecord = new TestableAttachmentFieldRecord($this->formRecord, 'id', $this->field, []);
        $this->attachedFile = $this->buildMockOfClass(AttachedFile::class);
        $this->attachmentFieldRecord->attachedFiles->add($this->attachedFile);
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoList = [$this->fileInfo];
    }
    
    protected function executeConstruct() {
        return new TestableAttachmentFieldRecord($this->formRecord, $this->id, $this->field, $this->fileInfoList);
    }
    
    function test_construct_setProperties() {
        $attachmentFieldRecord = $this->executeConstruct();
        $this->assertEquals($this->formRecord, $attachmentFieldRecord->formRecord);
        $this->assertEquals($this->id, $attachmentFieldRecord->id);
        $this->assertEquals($this->field, $attachmentFieldRecord->attachmentField);
        $this->assertFalse($attachmentFieldRecord->removed);
    }
    function test_construct_addAttachedFilesToCollection() {
        $attachmentFieldRecord = $this->executeConstruct();
        $this->assertEquals(1, $attachmentFieldRecord->attachedFiles->count());
        $this->assertInstanceOf(AttachedFile::class, $attachmentFieldRecord->attachedFiles->first());
    }
    
    protected function executeSetAttachedFiles() {
        $this->attachmentFieldRecord->setAttachedFiles($this->fileInfoList);
    }
    function test_setAttachedFiles_addAttachedFiledToCollection() {
        $this->executeSetAttachedFiles();
        $this->assertEquals(2, $this->attachmentFieldRecord->attachedFiles->count());
        $this->assertInstanceOf(AttachedFile::class, $this->attachmentFieldRecord->attachedFiles->last());
    }
    function test_setAttachedFiles_anAttachedFileReferToSameFileInfoAlreadyExistInCollection_skipAddingThisFileInfo() {
        $this->attachedFile->expects($this->any())
            ->method('getFileInfo')
            ->willReturn($this->fileInfo);
        $this->executeSetAttachedFiles();
        $this->assertEquals(1, $this->attachmentFieldRecord->attachedFiles->count());
    }
    function test_setAttachedFiles_anExistingAttachedFileReferToSameFileInfoAlreadyRemoved_addNewRecord() {
        $this->attachedFile->expects($this->any())
            ->method('getFileInfo')
            ->willReturn($this->fileInfo);
        $this->attachedFile->expects($this->any())
            ->method('isRemoved')
            ->willReturn(true);
        $this->executeSetAttachedFiles();
        $this->assertEquals(2, $this->attachmentFieldRecord->attachedFiles->count());
        $this->assertInstanceOf(AttachedFile::class, $this->attachmentFieldRecord->attachedFiles->last());
    }
    function test_setAttachedFiles_containAttachedFilesReferToFileInfoNoLongerAttached_removeThisObsoleteAttachedFiles() {
        $nonAttachedFileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->attachedFile->expects($this->atLeastOnce())
            ->method('getFileInfo')
            ->willReturn($nonAttachedFileInfo);
        $this->attachedFile->expects($this->once())
            ->method('remove');
        $this->executeSetAttachedFiles();
    }
    function test_setAttachedFiles_attachedFilesReferToObsoleteFileInfoAlreadyRemoved_ignoreRemovingThisAttachedFile() {
        $nonAttachedFileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->attachedFile->expects($this->atLeastOnce())
            ->method('getFileInfo')
            ->willReturn($nonAttachedFileInfo);
        $this->attachedFile->expects($this->atLeastOnce())
            ->method('isRemoved')
            ->willReturn(true);
        $this->attachedFile->expects($this->never())
            ->method('remove');
        $this->executeSetAttachedFiles();
    }
    
    function test_isReferToRemovedField_returnFieldRemovedStatus() {
        $this->field->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->assertTrue($this->attachmentFieldRecord->isReferToRemovedField());
    }
    
    function test_remove_setRemovedStatusTrue() {
        $this->attachmentFieldRecord->remove();
        $this->assertTrue($this->attachmentFieldRecord->removed);
    }
    
}

class TestableAttachmentFieldRecord extends AttachmentFieldRecord{
    public $formRecord, $id, $attachmentField, $removed;
    public $attachedFiles;
}

