<?php

namespace Firm\Application\Service;

use Firm\ {
    Application\Service\Firm\FirmFileInfoRepository,
    Domain\Model\Firm,
    Domain\Model\Firm\FirmFileInfo
};
use Tests\TestBase;

class UpdateFirmProfileTest extends TestBase
{
    protected $firmRepository, $firm;
    protected $firmFileInfoRepository, $firmFileInfo;
    protected $service;
    protected $firmId = "firmId", $firmFileInfoId = "firmFileInfoId", $displaySetting = "new display setting";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId)
                ->willReturn($this->firm);
        
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        $this->firmFileInfoRepository = $this->buildMockOfInterface(FirmFileInfoRepository::class);
        $this->firmFileInfoRepository->expects($this->any())
                ->method("aFirmFileInfoBelongsToFirm")
                ->with($this->firmId, $this->firmFileInfoId)
                ->willReturn($this->firmFileInfo);
        
        $this->service = new UpdateFirmProfile($this->firmRepository, $this->firmFileInfoRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->firmFileInfoId, $this->displaySetting);
    }
    public function test_execute_updateFirmProfile()
    {
        $this->firm->expects($this->once())
                ->method("updateProfile")
                ->with($this->firmFileInfo, $this->displaySetting);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->firmRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_emptyFirmFileInfoId_useNullLogo()
    {
        $this->firmFileInfoId = null;
        $this->firm->expects($this->once())
                ->method("updateProfile")
                ->with(null, $this->displaySetting);
        $this->execute();
    }
}
