<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;
use Shared\Domain\ {
    Model\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;

class ProgramParticipationFileUploadTest extends TestBase
{
    protected $service;
    protected $programParticipationFileInfoRepository;
    protected $programParticipationRepository;
    protected $uploadFile;
    
    protected $incubatorId = 'incubator-id', $programParticipationId = 'programParticipation-id';
    protected $fileInfoData, $contents = 'string represent stream resource';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationFileInfoRepository = $this->buildMockOfInterface(PersonnelFileInfoRepository::class);
        $this->programParticipationRepository = $this->buildMockOfClass(ProgramParticipationRepository::class);
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
        
        $this->service = new ProgramParticipationFileUpload($this->programParticipationFileInfoRepository, $this->programParticipationRepository, $this->uploadFile);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
            ->method('getName')
            ->willReturn('filename.jpg');
    }
    
    protected function execute()
    {
        return $this->service->execute($this->incubatorId, $this->programParticipationId, $this->fileInfoData, $this->contents);
    }
    
    public function test_execute_addProgramParticipationFileInfoToRepository()
    {
        $this->programParticipationFileInfoRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_executeUploadFile()
    {
        $this->uploadFile->expects($this->once())
            ->method('execute');
        $this->execute();
    }
}
