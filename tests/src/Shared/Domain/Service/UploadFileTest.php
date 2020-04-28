<?php

namespace Shared\Domain\Service;

use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class UploadFileTest extends TestBase
{
    protected $service;
    protected $fileRepository;
    protected $fileInfo, $filePath = '\path\to\filename.txt', $contents;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fileRepository = $this->buildMockOfInterface(FileRepository::class);
        $this->service = new UploadFile($this->fileRepository);
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfo->expects($this->any())
            ->method('getFullyQualifiedFileName')
            ->willReturn($this->filePath);
        $this->contents = 'mocked file content';
    }
    
    protected function execute()
    {
        return $this->service->execute($this->fileInfo, $this->contents);
    }
    
    protected function setFileRepositorWriteFileExpectationPassed() {
        $this->fileRepository->expects($this->any())
            ->method('write')
            ->with($this->filePath, $this->contents)
            ->willReturn(true);
    }
    
    public function test_execute_writeToFileRepository()
    {
        $this->fileRepository->expects($this->once())
            ->method('write')
            ->with($this->filePath, $this->contents)
            ->willReturn(true);
        $this->execute();
    }
    
    function test_execute_filePathAlreadyExist_throwEx() {
        $this->fileRepository->expects($this->once())
            ->method('has')
            ->with($this->filePath)
            ->willReturn(true);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "conflict: file name already exist";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    function test_execute_writeFileFailed_throwEx() {
        $this->fileRepository->expects($this->once())
            ->method('write')
            ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "internal server error: fail to write file to disc";
        $this->assertRegularExceptionThrowed($operation, 'Internal Server Error', $errorDetail);
    }
}

