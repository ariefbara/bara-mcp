<?php

namespace Shared\Domain\Model\FormRecord\AttachmentFieldRecord;

use Shared\Domain\Model\ {
    FileInfo,
    FormRecord\AttachmentFieldRecord
};
use Tests\TestBase;

class AttachedFileTest extends TestBase
{
    protected $attachmentFieldRecord, $fileInfo;
    protected $attachedFile;
    protected $id = 'attached-file-id';
    
    protected function setUp(): void {
        parent::setUp();
        $this->attachmentFieldRecord = $this->buildMockOfClass(AttachmentFieldRecord::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->attachedFile = new TestableAttachedFile($this->attachmentFieldRecord, 'id', $this->fileInfo);
    }
    
    function test_construct_setProperties() {
        $attachedFile = new TestableAttachedFile($this->attachmentFieldRecord, $this->id, $this->fileInfo);
        $this->assertEquals($this->attachmentFieldRecord, $attachedFile->attachmentFieldRecord);
        $this->assertEquals($this->id, $attachedFile->id);
        $this->assertEquals($this->fileInfo, $attachedFile->fileInfo);
        $this->assertFalse($attachedFile->removed);
    }
    
    function test_remove_setRemovedStatusTrue() {
        $this->attachedFile->remove();
        $this->assertTrue($this->attachedFile->removed);
    }
}

class TestableAttachedFile extends AttachedFile{
    public $attachmentFieldRecord, $id, $fileInfo, $removed;
}

