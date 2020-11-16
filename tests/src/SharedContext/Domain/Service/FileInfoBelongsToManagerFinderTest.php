<?php

namespace SharedContext\Domain\Service;

use Tests\TestBase;

class FileInfoBelongsToManagerFinderTest extends TestBase
{
    protected $firmId = "firmId", $managerId = "managerId";
    protected $fileInfoRepository;
    protected $finder;
    protected $fileInfoId = "fileInfoId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new FileInfoBelongsToManagerFinder($this->fileInfoRepository, $this->firmId, $this->managerId);
    }
    
    public function test_ofId_returnRepositoryFileInfoBelongsToManagerResult()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method("aFileInfoBelongsToManager")
                ->with($this->firmId, $this->managerId, $this->fileInfoId);
        
        $this->finder->ofId($this->fileInfoId);
    }
}
