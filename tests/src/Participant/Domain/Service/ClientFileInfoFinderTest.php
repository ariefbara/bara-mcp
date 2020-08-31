<?php

namespace Participant\Domain\Service;

use Tests\TestBase;

class ClientFileInfoFinderTest extends TestBase
{
    protected $service;
    protected $fileInfoRepository;
    protected $firmId = 'firmId', $clientId = 'clientId';
    
    protected $fileInfoId = 'fileInfoId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        
        $this->service = new ClientFileInfoFinder($this->fileInfoRepository, $this->firmId, $this->clientId);
    }
    
    public function test_ofId_returnFileInfoCorrespondToClientFromRepository()
    {
        $this->fileInfoRepository->expects($this->once())
                ->method('fileInfoOfClient')
                ->with($this->firmId, $this->clientId);
        $this->service->ofId($this->fileInfoId);
    }
}
