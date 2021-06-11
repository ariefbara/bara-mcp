<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\ProfileForm;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    
    protected $firmFileInfoId = "firmFileInfoId", $fileInfoData;
    protected $firmFileInfo, $displaySetting = "new display setting";
    protected $profileForm;
    protected $bioSearchFilterData;
    protected $bioSearchFilter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
        
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
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
    
    protected function executeSetBioSearchFilter()
    {
        $this->firm->setBioSearchFilter($this->bioSearchFilterData);
    }
    public function test_setBioSearchFilter_setBioSearchFilter()
    {
        $this->executeSetBioSearchFilter();
        $this->assertInstanceOf(BioSearchFilter::class, $this->firm->bioSearchFilter);
    }
    public function test_setBioSearchFilter_alreadyContainBioSearchFilter_updateExistingBioSearchFilter()
    {
        $this->firm->bioSearchFilter = $this->bioSearchFilter;
        $this->bioSearchFilter->expects($this->once())
                ->method('update')
                ->with($this->bioSearchFilterData);
        $this->executeSetBioSearchFilter();
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
    public $bioSearchFilter;
    
    function __construct()
    {
        parent::__construct();
    }
}
