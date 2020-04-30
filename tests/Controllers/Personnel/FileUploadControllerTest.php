<?php

namespace Tests\Controllers\Personnel;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};

class FileUploadControllerTest extends PersonnelTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('PersonnelFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $this->fileUploadUri = $this->personnelUri. "/file-uploads";
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->personnel->firm->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('PersonnelFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("firm_{$this->personnel->firm->id}");
    }
    
    public function test_upload()
    {
        $header = $this->personnel->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $personnelFileInfoEntry = [
            "Personnel_id" => $this->personnel->id,
            "removed" => false,
        ];
        $this->seeInDatabase("PersonnelFileInfo", $personnelFileInfoEntry);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
}
