<?php

namespace Participant\Domain\Service;

use Tests\TestBase;

class UserFileInfoFinderTest extends TestBase
{
    protected $service;
    protected $fileInfoRepository;
    protected $userId = 'userId';
    
    protected $fileInfoId = 'fileInfoId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        
        $this->service = new UserFileInfoFinder($this->fileInfoRepository, $this->userId);
    }
    
    public function test_ofId_returnFileInfo()
    {
        
        $this->fileInfoRepository->expects($this->once())
            ->method('fileInfoOfUser')
            ->with($this->userId, $this->fileInfoId);
        $this->service->ofId($this->fileInfoId);
    }
    
}
