<?php

namespace Tests\Controllers\User;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};

class FileUploadControllerTest extends UserTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->userUri. "/file-uploads";
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('UserFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("user_{$this->user->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('UserFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("user_{$this->user->id}");
    }
    
    public function test_upload()
    {
        $header = $this->user->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $userFileInfo = [
            "User_id" => $this->user->id,
            "removed" => false,
        ];
        $this->seeInDatabase("UserFileInfo", $userFileInfo);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
}
