<?php

namespace SharedContext\Domain\Service;

use Tests\TestBase;

class FileInfoBelongsToTeamFinderTest extends TestBase
{
    protected $firmId = "firmId", $teamId = "teamId";
    protected $fileInfoRepository;
    protected $finder;
    protected $fileInfoId = "fileInfoId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new FileInfoBelongsToTeamFinder($this->fileInfoRepository, $this->firmId, $this->teamId);
    }
    
    public function test_ofId_returnRepositoryFileInfoBelongsToTeamResult()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method("aFileInfoBelongsToTeam")
                ->with($this->firmId, $this->teamId, $this->fileInfoId);
        
        $this->finder->ofId($this->fileInfoId);
    }
}
