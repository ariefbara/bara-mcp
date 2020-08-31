<?php

namespace Client\Domain\Model\Client;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Tests\TestBase;
use Client\Domain\Model\Client;

class ClientFileInfoTest extends TestBase
{
    protected $clientFileInfo;
    protected $client;
    protected $id = 'new-id', $fileInfoData;
    
    protected $uploadFile, $contents = 'string-represent-file-content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->fileInfoData = new FileInfoData('docs.pdf', 1.2);
        $this->clientFileInfo = new TestableClientFileInfo($this->client, 'id', $this->fileInfoData);
        
        $this->uploadFile = $this->buildMockOfClass(UploadFile::class);
    }

    public function test_construct_setProperties()
    {
        $clientFileInfo = new TestableClientFileInfo($this->client, $this->id, $this->fileInfoData);
        $this->assertEquals($this->client, $clientFileInfo->client);
        $this->assertEquals($this->id, $clientFileInfo->id);
        $this->assertFalse($clientFileInfo->removed);

        $fileInfo = new FileInfo($this->id, $this->fileInfoData);
        $this->assertEquals($fileInfo, $clientFileInfo->fileInfo);
    }
    public function test_uploadContents_uploadContents()
    {
        $this->uploadFile->expects($this->once())
                ->method('execute')
                ->with($this->clientFileInfo->fileInfo, $this->contents);
        $this->clientFileInfo->uploadContents($this->uploadFile, $this->contents);
    }

}

class TestableClientFileInfo extends ClientFileInfo
{

    public $client;
    public $id;
    public $fileInfo;
    public $removed = false;

}
