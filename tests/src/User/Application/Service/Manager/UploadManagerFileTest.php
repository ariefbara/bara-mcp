<?php

namespace User\Application\Service\Manager;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use User\Domain\Model\ {
    Manager,
    Manager\ManagerFileInfo
};

class UploadManagerFileTest extends TestBase
{
    protected $managerFileInfoRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $uploadFile;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId";
    protected $fileInfoData;
    protected $contents = "string represent content";
    protected $managerFileInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->managerFileInfoRepository = $this->buildMockOfInterface(ManagerFileInfoRepository::class);
        $this->managerFileInfoRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
        
        $this->service = new UploadManagerFile($this->managerFileInfoRepository, $this->managerRepository, $this->uploadFile);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        
        $this->managerFileInfo = $this->buildMockOfClass(ManagerFileInfo::class);
        $this->manager->expects($this->any())
                ->method('saveFileInfo')
                ->with($this->nextId, $this->fileInfoData)
                ->willReturn($this->managerFileInfo);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->fileInfoData, $this->contents);
    }
    public function test_execute_addManagerFileInfoToRepository()
    {
        $this->managerFileInfoRepository->expects($this->once())
                ->method("add")
                ->with($this->managerFileInfo);
        $this->execute();
    }
    public function test_execute_uploadContentsInManagerFileInfo()
    {
        $this->managerFileInfo->expects($this->once())
                ->method('uploadContents')
                ->with($this->uploadFile, $this->contents);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
