<?php

namespace Team\Domain\Model\Team;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Team\Domain\Model\Team;
use Tests\TestBase;

class TeamFileInfoTest extends TestBase
{
    protected $team;
    protected $fileInfo;
    protected $teamFileInfo;
    
    protected $id = "nextId", $fileInfoData;
    
    protected $uploadFile, $contents = "contents";

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
        
        $this->teamFileInfo = new TestableTeamFileInfo($this->team, "id", $this->fileInfoData);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->teamFileInfo->fileInfo = $this->fileInfo;
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
    }
    
    public function test_construct_setProperties()
    {
        $teamFileInfo = new TestableTeamFileInfo($this->team, $this->id, $this->fileInfoData);
        $this->assertEquals($this->team, $teamFileInfo->team);
        $this->assertEquals($this->id, $teamFileInfo->id);
        $this->assertFalse($teamFileInfo->removed);
        
        $fileInfo = new FileInfo($this->id, $this->fileInfoData);
        $this->assertEquals($fileInfo, $teamFileInfo->fileInfo);
    }
    
    public function test_uploadContents_executeFileInfoUploadContents()
    {
        $this->uploadFile->expects($this->once())
                ->method("execute")
                ->with($this->fileInfo, $this->contents);
        $this->teamFileInfo->uploadContents($this->uploadFile, $this->contents);
    }

}

class TestableTeamFileInfo extends TeamFileInfo
{
    public $team;
    public $id;
    public $fileInfo;
    public $removed = false;
}
