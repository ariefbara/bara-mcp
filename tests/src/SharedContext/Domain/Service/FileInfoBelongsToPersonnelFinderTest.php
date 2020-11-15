<?php

namespace SharedContext\Domain\Service;

use Tests\TestBase;

class FileInfoBelongsToPersonnelFinderTest extends TestBase
{
    protected $firmId = "firmId", $personnelId = "personnelId";
    protected $fileInfoRepository;
    protected $finder;
    protected $fileInfoId = "fileInfoId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new FileInfoBelongsToPersonnelFinder($this->fileInfoRepository, $this->firmId, $this->personnelId);
    }
    
    public function test_ofId_returnRepositoryFileInfoBelongsToPersonnelResult()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method("aFileInfoBelongsToPersonnel")
                ->with($this->firmId, $this->personnelId, $this->fileInfoId);
        
        $this->finder->ofId($this->fileInfoId);
    }
}
