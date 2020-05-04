<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;
use Shared\Domain\ {
    Model\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;

class ParticipantFileUploadTest extends TestBase
{
    protected $service;
    protected $participantFileInfoRepository;
    protected $programParticipationRepository;
    protected $uploadFile;
    
    protected $incubatorId = 'incubator-id', $programParticipationId = 'programParticipation-id';
    protected $fileInfoData, $contents = 'string represent stream resource';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantFileInfoRepository = $this->buildMockOfInterface(ParticipantFileInfoRepository::class);
        $this->programParticipationRepository = $this->buildMockOfClass(ProgramParticipationRepository::class);
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
        
        $this->service = new ParticipantFileUpload($this->participantFileInfoRepository, $this->programParticipationRepository, $this->uploadFile);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
            ->method('getName')
            ->willReturn('filename.jpg');
    }
    
    protected function execute()
    {
        return $this->service->execute($this->incubatorId, $this->programParticipationId, $this->fileInfoData, $this->contents);
    }
    
    public function test_execute_addParticipantFileInfoToRepository()
    {
        $this->participantFileInfoRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_executeUploadFile()
    {
        $this->uploadFile->expects($this->once())
            ->method('execute');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->participantFileInfoRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }
}
