<?php

namespace Tests\Controllers\Manager;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};

class FileUploadControllerTest extends ManagerTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->managerUri. "/upload-personal-file";
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ManagerFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->manager->firm->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ManagerFileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->manager->firm->id}");
    }
    
    public function test_upload()
    {
echo $this->fileUploadUri;
        $header = $this->manager->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $managerFileInfo = [
            "Manager_id" => $this->manager->id,
            "removed" => false,
        ];
        $this->seeInDatabase("ManagerFileInfo", $managerFileInfo);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
}
