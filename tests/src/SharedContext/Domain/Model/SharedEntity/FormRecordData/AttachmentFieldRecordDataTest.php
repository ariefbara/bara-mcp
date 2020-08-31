<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecordData;

use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\TestBase;

class AttachmentFieldRecordDataTest extends TestBase
{

    protected $input;
    protected $fileInfoFinder;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoFinder = $this->buildMockOfInterface(IFileInfoFinder::class);
        $this->input = new TestableAttachmentFieldRecordData($this->fileInfoFinder, 'id');
    }

    public function test_construct_setProperties()
    {
        $input = new TestableAttachmentFieldRecordData($this->fileInfoFinder, $this->id);
        $this->assertEquals($this->fileInfoFinder, $input->fileInfoFinder);
        $this->assertEquals($this->id, $input->attachmentFieldId);
        $this->assertEquals([], $input->fileInfoCollection);
    }

    public function test_add_addFileInfoToStorage()
    {
        $fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoFinder->expects($this->once())
                ->method('ofId')
                ->with($fileInfoId = 'fileInfoId')
                ->willReturn($fileInfo);
        $this->input->add($fileInfoId);

        $this->assertEquals([$fileInfo], $this->input->fileInfoCollection);
    }

}

class TestableAttachmentFieldRecordData extends AttachmentFieldRecordData
{

    public $fileInfoFinder;
    public $attachmentFieldId;
    public $fileInfoCollection;

}
