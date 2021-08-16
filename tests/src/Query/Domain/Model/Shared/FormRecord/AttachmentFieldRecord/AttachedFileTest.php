<?php

namespace Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;

use Query\Domain\Model\Shared\FileInfo;
use Tests\TestBase;

class AttachedFileTest extends TestBase
{
    protected $attachedFile;
    protected $fileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->attachedFile = new TestableAttachedFile();
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->attachedFile->fileInfo = $this->fileInfo;
    }
    
    public function test_getFileLocation_returnFileInfoLocation()
    {
        $this->fileInfo->expects($this->once())
                ->method('getFullyQualifiedFileName')
                ->willReturn($location = "/path/to/file.jpg");
        $this->assertEquals($location, $this->attachedFile->getFileLocation());
    }
}

class TestableAttachedFile extends AttachedFile
{
    public $attachmentFieldRecord;
    public $id;
    public $fileInfo;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
