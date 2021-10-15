<?php

namespace Tests\Controllers\Manager;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};

class UploadFileControllerTest extends ManagerTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->managerUri. "/upload-firm-file";
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->firm->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->firm->id}");
    }
    
    public function test_upload()
    {
        $header = $this->manager->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $firmFileInfoEntry = [
            "Firm_id" => $this->firm->id,
            "removed" => false,
        ];
        $this->seeInDatabase("FirmFileInfo", $firmFileInfoEntry);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
/*
    public function test_upload_removedManager_401()
    {
        $header = $this->removedManager->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(401);
    }
 * 
 */
}
