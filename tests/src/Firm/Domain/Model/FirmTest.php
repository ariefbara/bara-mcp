<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\ClientRegistrationData;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\ProfileForm;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Model\Firm\TeamData;
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
    protected $clientId = 'clientId', $clientRegistrationData;
    
    protected $teamId = 'teamId', $teamName = 'new team name', $client, $memberPosition = 'new member position';

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = new TestableFirm();
        $this->fileInfoData = new FileInfoData('filename.ext', null);
        
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        
        $this->bioSearchFilterData = $this->buildMockOfClass(BioSearchFilterData::class);
        $this->bioSearchFilter = $this->buildMockOfClass(BioSearchFilter::class);
        
        $this->clientRegistrationData = new ClientRegistrationData('firstname', 'lastname', 'client@email.org', 'password123');
        
        $this->client = $this->buildMockOfClass(Client::class);
    }
    
    //
    protected function createFileInfo()
    {
        return $this->firm->createFileInfo($this->firmFileInfoId, $this->fileInfoData);
    }
    public function test_createFileInfo_returnFirmFileInfo()
    {
        $this->assertInstanceOf(FirmFileInfo::class, $this->createFileInfo());
    }
    public function test_createFileInfo_setIdentifierAsFileInfoDataBucket()
    {
        $this->createFileInfo();
        $this->assertEquals($this->firm->identifier, $this->fileInfoData->bucketName);
    }
    
    //
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
    
    //
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
    
    //
    protected function createClient()
    {
        return $this->firm->createClient($this->clientId, $this->clientRegistrationData);
    }
    public function test_createClient_returnClient()
    {
        $this->assertInstanceOf(Client::class, $this->createClient());
    }
    
    //
    protected function createTeam()
    {
        $teamData = new TeamData($this->teamName);
        $teamData->addMemberData(new MemberData($this->client, $this->memberPosition));
        return $this->firm->createTeam($this->teamId, $teamData);
    }
    public function test_createTeam_returnTeam()
    {
        $this->assertInstanceOf(Team::class, $this->createTeam());
    }
    
}

class TestableFirm extends Firm
{
    public $id = 'firmId';
    public $name;
    public $identifier = 'firm-identifier';
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
