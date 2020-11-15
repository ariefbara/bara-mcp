<?php

namespace SharedContext\Domain\Service;

use Tests\TestBase;

class FileInfoBelongsToUserFinderTest extends TestBase
{
    protected $userId = "userId";
    protected $fileInfoRepository;
    protected $finder;
    protected $fileInfoId = "fileInfoId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->finder = new FileInfoBelongsToUserFinder($this->fileInfoRepository, $this->userId);
    }
    
    public function test_ofId_returnRepositoryFileInfoBelongsToUserResult()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method("aFileInfoBelongsToUser")
                ->with($this->userId, $this->fileInfoId);
        
        $this->finder->ofId($this->fileInfoId);
    }
}
