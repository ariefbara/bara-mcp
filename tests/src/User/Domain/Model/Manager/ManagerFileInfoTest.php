<?php

namespace User\Domain\Model\Manager;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use User\Domain\Model\Manager;

class ManagerFileInfoTest extends TestBase
{
    protected $managerFileInfo;
    protected $manager;
    protected $id = 'new-id', $fileInfoData;
    
    protected $uploadFile, $contents = 'string-represent-file-content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->fileInfoData = new FileInfoData('docs.pdf', 1.2);
        $this->managerFileInfo = new TestableManagerFileInfo($this->manager, 'id', $this->fileInfoData);
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
    }

    public function test_construct_setProperties()
    {
        $managerFileInfo = new TestableManagerFileInfo($this->manager, $this->id, $this->fileInfoData);
        $this->assertEquals($this->manager, $managerFileInfo->manager);
        $this->assertEquals($this->id, $managerFileInfo->id);
        $this->assertFalse($managerFileInfo->removed);

        $fileInfo = new FileInfo($this->id, $this->fileInfoData);
        $this->assertEquals($fileInfo, $managerFileInfo->fileInfo);
    }
    public function test_uploadContents_uploadContents()
    {
        $this->uploadFile->expects($this->once())
                ->method('execute')
                ->with($this->managerFileInfo->fileInfo, $this->contents);
        $this->managerFileInfo->uploadContents($this->uploadFile, $this->contents);
    }

}

class TestableManagerFileInfo extends ManagerFileInfo
{

    public $manager;
    public $id;
    public $fileInfo;
    public $removed = false;

}
