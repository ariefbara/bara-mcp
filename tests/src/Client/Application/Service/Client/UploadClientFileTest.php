<?php

namespace Client\Application\Service\Client;

use SharedContext\Domain\{
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use Client\{
    Application\Service\ClientRepository,
    Domain\Model\Client,
    Domain\Model\Client\ClientFileInfo
};

class UploadClientFileTest extends TestBase
{

    protected $service;
    protected $clientFileInfoRepository, $nextId = 'nextId';
    protected $clientRepository, $client;
    protected $uploadFile;
    protected $firmId = 'firmid', $clientId = 'clientId';
    protected $fileInfoData, $contents = 'string represent stream resource';
    protected $clientFileInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientFileInfoRepository = $this->buildMockOfInterface(ClientFileInfoRepository::class);
        $this->clientFileInfoRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);

        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);

        $this->service = new UploadClientFile($this->clientFileInfoRepository, $this->clientRepository, $this->uploadFile);

        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
                ->method('getName')
                ->willReturn('filename.jpg');

        $this->clientFileInfo = $this->buildMockOfClass(ClientFileInfo::class);
        $this->client->expects($this->any())
                ->method('createClientFileInfo')
                ->with($this->nextId, $this->fileInfoData)
                ->willReturn($this->clientFileInfo);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->fileInfoData, $this->contents);
    }

    public function test_execute_addClientFileInfoToRepository()
    {
        $this->clientFileInfoRepository->expects($this->once())
                ->method('add')
                ->with($this->clientFileInfo);
        $this->execute();
    }

    public function test_execute_ClientFileInfoUploadContents()
    {
        $this->clientFileInfo->expects($this->once())
                ->method('uploadContents')
                ->with($this->uploadFile, $this->contents);
        $this->execute();
    }

      public function test_execute_returnNextId()
      {
          $this->assertEquals($this->nextId, $this->execute());
      }
}
