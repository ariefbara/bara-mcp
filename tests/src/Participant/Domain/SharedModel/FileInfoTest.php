<?php

namespace Participant\Domain\SharedModel;

use Participant\Domain\DependencyModel\ {
    Firm\Client,
    Firm\Client\ClientFileInfo,
    Firm\Team,
    Firm\Team\TeamFileInfo,
    User\UserFileInfo
};
use Tests\TestBase;

class FileInfoTest extends TestBase
{
    protected $clientFileInfo;
    protected $userFileInfo;
    protected $teamFileInfo;
    protected $fileInfo;
    
    protected $client;
    protected $userId = "userId";
    protected $team;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientFileInfo = $this->buildMockOfClass(ClientFileInfo::class);
        $this->userFileInfo = $this->buildMockOfClass(UserFileInfo::class);
        $this->teamFileInfo = $this->buildMockOfClass(TeamFileInfo::class);
        
        $this->fileInfo = new TestableFileInfo();
        $this->fileInfo->clientFileInfo = $this->clientFileInfo;
        $this->fileInfo->userFileInfo = $this->userFileInfo;
        $this->fileInfo->teamFileInfo = $this->teamFileInfo;
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->team = $this->buildMockOfClass(Team::class);
    }
    
    protected function executeBelongsToClient()
    {
        return $this->fileInfo->belongsToClient($this->client);
    }
    public function test_belongsToClient_returnClientFileInfoBelongsToClientResult()
    {
        $this->clientFileInfo->expects($this->once())
                ->method("belongsToClient")
                ->with($this->client);
        $this->executeBelongsToClient();
    }
    public function test_belongsToClient_notClientFileInfo_returnFalse()
    {
        $this->fileInfo->clientFileInfo = null;
        $this->assertFalse($this->executeBelongsToClient());
    }
    
    protected function executeBelongsToUser()
    {
        return $this->fileInfo->belongsToUser($this->userId);
    }
    public function test_belongsToUser_returnUserFileInfoBelongsToUserResult()
    {
        $this->userFileInfo->expects($this->once())
                ->method("belongsToUser")
                ->with($this->userId);
        $this->executeBelongsToUser();
    }
    public function test_belongsToUser_notUserFileInfo_returnFalse()
    {
        $this->fileInfo->userFileInfo = null;
        $this->assertFalse($this->executeBelongsToUser());
    }
    
    protected function executeBelongsToTeam()
    {
        return $this->fileInfo->belongsToTeam($this->team);
    }
    public function test_belongsToTeam_returnTeamFileInfoBelongsToTeamResult()
    {
        $this->teamFileInfo->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team);
        $this->executeBelongsToTeam();
    }
    public function test_belongsToTeam_notTeamFileInfo_returnFalse()
    {
        $this->fileInfo->teamFileInfo = null;
        $this->assertFalse($this->executeBelongsToTeam());
    }
}

class TestableFileInfo extends FileInfo
{
    public $id = "id";
    public $clientFileInfo;
    public $userFileInfo;
    public $teamFileInfo;
    
    function __construct()
    {
        parent::__construct();
    }
}
