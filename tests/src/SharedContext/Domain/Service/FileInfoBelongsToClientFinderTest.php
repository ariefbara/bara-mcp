<?php

namespace SharedContext\Domain\Service;

use Tests\TestBase;

class FileInfoBelongsToClientFinderTest extends TestBase
{
    protected $firmId = "firmId", $clientId = "clientId";
    protected $fileInfoRepository;
    protected $finder;
    protected $fileInfoId = "fileInfoId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new FileInfoBelongsToClientFinder($this->fileInfoRepository, $this->firmId, $this->clientId);
    }
    
    public function test_ofId_returnRepositoryFileInfoBelongsToClientResult()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method("aFileInfoBelongsToClient")
                ->with($this->firmId, $this->clientId, $this->fileInfoId);
        
        $this->finder->ofId($this->fileInfoId);
    }
}
