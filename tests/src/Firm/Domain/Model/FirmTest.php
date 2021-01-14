<?php

namespace Firm\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\ClientCVForm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\ProfileForm;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class FirmTest extends TestBase
{
    protected $firm;
    
    protected $firmFileInfoId = "firmFileInfoId", $fileInfoData;
    protected $firmFileInfo, $displaySetting = "new display setting";
    protected $profileForm, $clientCVForm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
        
        $this->firm->clientCVForms = new ArrayCollection();
        $this->clientCVForm = $this->buildMockOfClass(ClientCVForm::class);
        $this->firm->clientCVForms->add($this->clientCVForm);
        
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
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
    
    protected function executeAssignClientCVForm()
    {
        return $this->firm->assignClientCVForm($this->profileForm);
    }
    public function test_assigneClientCVForm_addClientCVFormToRepository()
    {
        $this->executeAssignClientCVForm();
        $this->assertEquals(2, $this->firm->clientCVForms->count());
        $this->assertInstanceOf(ClientCVForm::class, $this->firm->clientCVForms->last());
    }
    public function test_assignClientCVForm_aClientCVFormCorresponndWithSameProfileFormAlreadyExist_enableThisClientCVForm()
    {
        $this->clientCVForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->with($this->profileForm)
                ->willReturn(true);
        $this->clientCVForm->expects($this->once())
                ->method("enable");
        $this->executeAssignClientCVForm();
    }
    public function test_assignClientCVForm_aClientCVFormCorrespondWithSameProfileFormAlreadyExist_preventAddNewClientCVForm()
    {
        $this->clientCVForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->willReturn(true);
        $this->executeAssignClientCVForm();
        $this->assertEquals(1, $this->firm->clientCVForms->count());
    }
    public function test_assignClientCVForm_returnClientCVFormId()
    {
        $this->clientCVForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->willReturn(true);
        $this->clientCVForm->expects($this->once())
                ->method("getId")
                ->willReturn($id = "id");
        $this->assertEquals($id, $this->executeAssignClientCVForm());
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
    public $clientCVForms;
    
    function __construct()
    {
        parent::__construct();
    }
}
