<?php

namespace Client\Domain\Service;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class ParticipantFileInfoFinderTest extends TestBase
{
    protected $service;
    protected $participantFileInfoRepository;
    protected $programParticipationCompositionId;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantFileInfoRepository = $this->buildMockOfInterface(ParticipantFileInfoRepository::class);
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        
        $this->service = new ParticipantFileInfoFinder($this->participantFileInfoRepository, $this->programParticipationCompositionId);
    }
    
    public function test_ofId_returnFileInfoFromProgramParticipationFileInfoRepository()
    {
        $fileInfo = $this->buildMockOfClass(FileInfo::class);
        
        $this->participantFileInfoRepository->expects($this->once())
            ->method('fileInfoOf')
            ->with($this->programParticipationCompositionId, $fileInfoId = 'file-info-id')
            ->willReturn($fileInfo);
        
        $this->assertEquals($fileInfo, $this->service->aTeamMembershipCorrespondWithTeam($fileInfoId));
    }
}
