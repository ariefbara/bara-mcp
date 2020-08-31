<?php

namespace User\Domain\Model\User;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use User\Domain\Model\User;

class UserFileInfoTest extends TestBase
{
    protected $userFileInfo;
    protected $user;
    protected $id = 'new-id', $fileInfoData;
    
    protected $uploadFile, $contents = 'string-represent-file-content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfClass(User::class);
        $this->fileInfoData = new FileInfoData('docs.pdf', 1.2);
        $this->userFileInfo = new TestableUserFileInfo($this->user, 'id', $this->fileInfoData);
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
    }

    public function test_construct_setProperties()
    {
        $userFileInfo = new TestableUserFileInfo($this->user, $this->id, $this->fileInfoData);
        $this->assertEquals($this->user, $userFileInfo->user);
        $this->assertEquals($this->id, $userFileInfo->id);
        $this->assertFalse($userFileInfo->removed);

        $fileInfo = new FileInfo($this->id, $this->fileInfoData);
        $this->assertEquals($fileInfo, $userFileInfo->fileInfo);
    }
    public function test_uploadContents_uploadContents()
    {
        $this->uploadFile->expects($this->once())
                ->method('execute')
                ->with($this->userFileInfo->fileInfo, $this->contents);
        $this->userFileInfo->uploadContents($this->uploadFile, $this->contents);
    }

}

class TestableUserFileInfo extends UserFileInfo
{

    public $user;
    public $id;
    public $fileInfo;
    public $removed = false;

}
