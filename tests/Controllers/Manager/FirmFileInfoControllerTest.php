<?php

namespace Tests\Controllers\Manager;

class FirmFileInfoControllerTest extends ExtendedManagerTestCase
{
    protected $fileInfoInput = [
        'name' => 'newfile.mp4',
    ];
    protected $firmFileInfoUri;


    protected function setUp(): void
    {
        parent::setUp();
        $this->firmFileInfoUri = $this->managerUri . "/firm-file-infos";
        //
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        //
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
    }
    
    //
    protected function create()
    {
        $this->persistManagerDependency();
        $this->post($this->firmFileInfoUri, $this->fileInfoInput, $this->manager->token);
    }
    public function test_create_201()
    {
$this->disableExceptionHandling();
        $this->create();
        $this->seeStatusCode(201);
        
        $this->seeJsonContains([
            'name' => $this->fileInfoInput['name'],
        ]);
        
        $this->seeInDatabase('FirmFileInfo', [
            'Firm_id' => $this->manager->firm->id,
        ]);
        
        $this->seeInDatabase('FileInfo', [
            'name' => $this->fileInfoInput['name'],
            'bucketName' => $this->manager->firm->identifier,
        ]);
$this->seeJsonContains(['print']);
    }
}
