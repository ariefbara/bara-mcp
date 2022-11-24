<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use SharedContext\Domain\Service\FileRepository;
use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class UploadFileTest extends ParticipantTaskTestBase
{

    protected $fileRepository;
    protected $task;
    //
    protected $payload, $fileInfoData, $contents = 'string represent file contents', $fullyQualifiedName = 'string represent file name';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupParticipantFileInfoDependency();

        $this->fileRepository = $this->buildMockOfInterface(FileRepository::class);
        
        $this->task = new UploadFile($this->participantFileInfoRepository, $this->fileRepository);
        
        //
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->payload = new UploadFilePayload($this->fileInfoData, $this->contents);
    }
    
    //
    protected function execute()
    {
        $this->participantFileInfoRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->participantFileInfoId);
        
        $this->participant->expects($this->any())
                ->method('uploadFile')
                ->with($this->participantFileInfoId, $this->fileInfoData)
                ->willReturn($this->participantFileInfo);
        
        $this->participantFileInfo->expects($this->any())
                ->method('getFullyQualifiedFileName')
                ->willReturn($this->fullyQualifiedName);
        
        $this->task->execute($this->participant, $this->payload);
    }
    public function test_execute_addParticipantFileInfoUploadedByParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('uploadFile')
                ->with($this->participantFileInfoId, $this->fileInfoData)
                ->willReturn($this->participantFileInfo);
        $this->participantFileInfoRepository->expects($this->once())
                ->method('add')
                ->with($this->participantFileInfo);
        $this->execute();
    }
    public function test_execute_writeContentsToStorage()
    {
        $this->participantFileInfo->expects($this->once())
                ->method('getFullyQualifiedFileName')
                ->willReturn($this->fullyQualifiedName);
        
        $this->fileRepository->expects($this->once())
                ->method('write')
                ->with($this->fullyQualifiedName, $this->contents);
        
        $this->execute();
    }
    public function test_execute_fileAlreadyExistInStorage_conflict()
    {
        $this->fileRepository->expects($this->once())
                ->method('has')
                ->with($this->fullyQualifiedName)
                ->willReturn(true);
        
        $this->assertRegularExceptionThrowed(function () {
            $this->execute();
        }, 'Conflict', 'file name already used');
    }
    public function test_execute_setUploadedFileInfoId()
    {
        $this->execute();
        $this->assertSame($this->participantFileInfoId, $this->payload->uploadedFileInfoId);
    }

}
