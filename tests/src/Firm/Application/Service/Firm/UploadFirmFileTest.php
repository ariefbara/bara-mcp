<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm
};
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;

class UploadFirmFileTest extends TestBase
{
    protected $firmFileInfoRepository, $nextId = "nextId";
    protected $firmRepository, $firm;
    protected $uploadFile;
    protected $service;
    protected $firmId = "firmId", $fileInfoData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmFileInfoRepository = $this->buildMockOfInterface(FirmFileInfoRepository::class);
        $this->firmFileInfoRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId)
                ->willReturn($this->firm);
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
        
        $this->service = new UploadFirmFile($this->firmFileInfoRepository, $this->firmRepository, $this->uploadFile);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->fileInfoData);
    }
    public function test_execute_addFirmFileInfoToRepository()
    {
        $this->firm->expects($this->once())
                ->method("createFileInfo")
                ->with($this->nextId, $this->fileInfoData);
        $this->firmFileInfoRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
