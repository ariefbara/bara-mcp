<?php

namespace Tests\Controllers\Client;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};

class FileUploadControllerTest extends ClientTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->clientUri. "/file-uploads";
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ClientFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("client_{$this->client->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ClientFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
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
        
        $clientFileInfo = [
            "Client_id" => $this->client->id,
            "removed" => false,
        ];
        $this->seeInDatabase("ClientFileInfo", $clientFileInfo);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
}
