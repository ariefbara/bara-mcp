<?php

namespace Client\Domain\Service;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class ProgramParticipationFileInfoFinderTest extends TestBase
{
    protected $service;
    protected $programParticipationFileInfoRepository;
    protected $programParticipationCompositionId;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationFileInfoRepository = $this->buildMockOfInterface(ProgramParticipationFileInfoRepository::class);
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        
        $this->service = new ProgramParticipationFileInfoFinder($this->programParticipationFileInfoRepository, $this->programParticipationCompositionId);
    }
    
    public function test_ofId_returnFileInfoFromProgramParticipationFileInfoRepository()
    {
        $fileInfo = $this->buildMockOfClass(FileInfo::class);
        
        $this->programParticipationFileInfoRepository->expects($this->once())
            ->method('fileInfoOf')
            ->with($this->programParticipationCompositionId, $fileInfoId = 'file-info-id')
            ->willReturn($fileInfo);
        
        $this->assertEquals($fileInfo, $this->service->ofId($fileInfoId));
    }
}
