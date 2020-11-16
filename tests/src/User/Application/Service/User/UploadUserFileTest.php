<?php

namespace User\Application\Service\User;

use SharedContext\Domain\{
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use User\{
    Application\Service\UserRepository,
    Domain\Model\User,
    Domain\Model\User\UserFileInfo
};

class UploadUserFileTest extends TestBase
{

    protected $service;
    protected $userFileInfoRepository, $nextId = 'nextId';
    protected $userRepository, $user;
    protected $uploadFile;
    protected $userId = 'userId';
    protected $fileInfoData, $contents = 'string represent stream resource';
    protected $userFileInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFileInfoRepository = $this->buildMockOfInterface(UserFileInfoRepository::class);
        $this->userFileInfoRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method('ofId')
                ->willReturn($this->user);

        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);

        $this->service = new UploadUserFile($this->userFileInfoRepository, $this->userRepository, $this->uploadFile);

        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
                ->method('getName')
                ->willReturn('filename.jpg');

        $this->userFileInfo = $this->buildMockOfClass(UserFileInfo::class);
        $this->user->expects($this->any())
                ->method('createUserFileInfo')
                ->with($this->nextId, $this->fileInfoData)
                ->willReturn($this->userFileInfo);
    }

    protected function execute()
    {
        return $this->service->execute($this->userId, $this->fileInfoData, $this->contents);
    }

    public function test_execute_addUserFileInfoToRepository()
    {
        $this->userFileInfoRepository->expects($this->once())
                ->method('add')
                ->with($this->userFileInfo);
        $this->execute();
    }

    public function test_execute_UserFileInfoUploadContents()
    {
        $this->userFileInfo->expects($this->once())
                ->method('uploadContents')
                ->with($this->uploadFile, $this->contents);
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
