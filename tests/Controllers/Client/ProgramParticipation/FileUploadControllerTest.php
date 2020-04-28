<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};
use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Shared\RecordOfFileInfo
};

class FileUploadControllerTest extends ProgramParticipationTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->programParticipationUri. "/{$this->programParticipation->id}/file-uploads";
        $this->connection->table('FileInfo')->truncate();
        $root = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("client_{$this->client->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        
        $root = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("client_{$this->client->id}");
    }
    
    public function test_upload()
    {
        $header = $this->client->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $participantFileInfoEntry = [
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase("ParticipantFileInfo", $participantFileInfoEntry);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
}
