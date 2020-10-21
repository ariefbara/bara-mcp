<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\FirmFileInfo;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    
    protected $firmFileInfoId = "firmFileInfoId", $fileInfoData;
    protected $firmFileInfo, $displaySetting = "new display setting";

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
    }
    
    public function test_createFileInfo_returnFirmFileInfo()
    {
        $firmFileInfo = new FirmFileInfo($this->firm, $this->firmFileInfoId, $this->fileInfoData);
        $this->assertEquals($firmFileInfo, $this->firm->createFileInfo($this->firmFileInfoId, $this->fileInfoData));
    }
    
    protected function executeUpdateProfile()
    {
        $this->firm->updateProfile($this->firmFileInfo, $this->displaySetting);
    }
    
    public function test_updateProfile_setLogoAndDisplaySetting()
    {
        $this->executeUpdateProfile();
        $this->assertEquals($this->firmFileInfo, $this->firm->logo);
        $this->assertEquals($this->displaySetting, $this->firm->displaySetting);
    }
    public function test_updateProfile_emptyFirmFileInfo_setLogoNull()
    {
        $this->firmFileInfo = null;
        $this->executeUpdateProfile();
        $this->assertNull($this->firm->logo);
    }
}

class TestableFirm extends Firm
{
    public $id = 'firmId';
    public $name;
    public $identifier;
    public $firmWhitelableInfo;
    public $logo;
    public $displaySetting;
    public $suspended = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
