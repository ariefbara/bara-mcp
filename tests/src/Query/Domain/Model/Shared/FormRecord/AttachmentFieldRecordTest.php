<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord\AttachedFile;
use Tests\TestBase;

class AttachmentFieldRecordTest extends TestBase
{
    protected $record;
    protected $field;
    protected $attachedFileOne;
    protected $attachedFileTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->record = new TestableAttachmentFieldRecord();
        $this->record->attachedFiles = new ArrayCollection();
        
        $this->field = $this->buildMockOfClass(AttachmentField::class);
        $this->record->attachmentField = $this->field;
        
        $this->attachedFileOne = $this->buildMockOfClass(AttachedFile::class);
        $this->attachedFileTwo = $this->buildMockOfClass(AttachedFile::class);
    }
    
    public function test_isActiveFieldRecordCorrespondWith_activeRecordCorrespondToSameField_returnTrue()
    {
        $this->assertTrue($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
    public function test_isActiveFieldRecordCorrespondWith_removedRecord_returnFalse()
    {
        $this->record->removed = true;
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
    public function test_isActiveFieldRecordCorrespondWith_differentField_returnFalse()
    {
        $this->record->attachmentField = $this->buildMockOfClass(AttachmentField::class);
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
    
    protected function getStringOfAttachedFileLocationList()
    {
        $this->record->attachedFiles->add($this->attachedFileOne);
        $this->record->attachedFiles->add($this->attachedFileTwo);
        return $this->record->getStringOfAttachedFileLocationList();
    }
    public function test_getStringOfAttachedFileLocationList_returnListOfAttachedFileLocationInString()
    {
        $this->attachedFileOne->expects($this->once())
                ->method('getFileLocation')
                ->willReturn($fileOneLocation = '/path/to/file/one.jpg');
        $this->attachedFileTwo->expects($this->once())
                ->method('getFileLocation')
                ->willReturn($fileTwoLocation = '/path/to/file/two.jpg');
        
        $result = "{$fileOneLocation}\r\n{$fileTwoLocation}";
        $this->assertEquals($result, $this->getStringOfAttachedFileLocationList());
    }
    public function test_getStringOfAttachedFileLocationList_containRemovedAttachedFile_excludeRemovedAttachment()
    {
        $this->attachedFileOne->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->attachedFileOne->expects($this->never())
                ->method('getFileLocation');
        
        $this->attachedFileTwo->expects($this->once())
                ->method('getFileLocation')
                ->willReturn($fileTwoLocation = '/path/to/file/two.jpg');
        
        $result = "{$fileTwoLocation}";
        $this->assertEquals($result, $this->getStringOfAttachedFileLocationList());
    }
}

class TestableAttachmentFieldRecord extends AttachmentFieldRecord
{
    public $formRecord;
    public $id;
    public $attachmentField;
    public $attachedFiles;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
