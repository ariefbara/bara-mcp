<?php

namespace SharedContext\Domain\Model\SharedEntity;

use SharedContext\Domain\Event\FileInfoCreatedEvent;
use Tests\TestBase;

class FileInfoTest extends TestBase
{
    protected $fileInfo;
    protected $id = 'fileInfoId', $name = 'filename.jpg', $size = 3.4, $bucketName = 'bucket', $directory = 'directory', $contentType = 'application/pdf';
    protected $folders = ['path', 'to', 'folder'];
    
    protected function setUp(): void {
        parent::setUp();
        $request = (new FileInfoData('file_name.pdf', 1.1))->setBucketName('buck')->setDirectory('dir');
        $request->addFolder('path');
        $request->addFolder('to');
        $request->addFolder('folder');
        $this->fileInfo = new TestableFileInfo('id', $request);
        $this->fileInfo->recordedEvents = [];
    }
    
    protected function getFileInfoData() {
        $fileInfoData =  (new FileInfoData($this->name, $this->size))
                ->setBucketName($this->bucketName)
                ->setContentType($this->contentType)
                ->setDirectory($this->directory);
        foreach ($this->folders as $folder) {
            $fileInfoData->addFolder($folder);
        }
        return $fileInfoData;
    }
    protected function executeConstruct() {
        return new TestableFileInfo($this->id, $this->getFileInfoData());
    }
    function test_construct_setProperties() {
        $fileInfo = $this->executeConstruct();
        $this->assertEquals($this->id, $fileInfo->id);
        $this->assertEquals($this->folders, $fileInfo->folders);
        $this->assertEquals($this->name, $fileInfo->name);
        $this->assertEquals($this->size, $fileInfo->size);
    }
    public function test_construct_setBucketContentTypeAndObjectName()
    {
        $fileInfo = $this->executeConstruct();
        $this->assertSame($this->bucketName, $fileInfo->bucketName);
        $this->assertSame($this->contentType, $fileInfo->contentType);
        $this->assertSame("{$this->directory}/{$this->id}", $fileInfo->objectName);
    }
    public function test_construct_emptyDirectory_setObjectNameWithoutDirectory()
    {
        $this->directory = null;
        $fileInfo = $this->executeConstruct();
        $this->assertSame("{$this->id}", $fileInfo->objectName);
    }
    function test_construct_invalidNameFormat_throwEx() {
        $this->name = 'invalid filename';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: file name is required and must include extension";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    function test_construct_emptyName_throwEx() {
        $this->name = "";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: file name is required and must include extension";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    function test_construct_nameContainNonWordsAndOrHypenChar_throwEx() {
        $this->name = "contain&^&^%&^%(*&_char.jpg";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: file name is required and must include extension";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    function test_construct_folderNameContainNonWordOrHypenChar_removeTheseChar() {
        $this->folders['0'] = 'root-(*&(*&(&path.,_';
        $fileInfo = $this->executeConstruct();
        $this->assertEquals('root-path_', $fileInfo->folders['0']);
    }
    public function test_construct_recordFileInfoCreatedEvent()
    {
        $fileInfo = $this->executeConstruct();
        $event = new FileInfoCreatedEvent($this->bucketName, "{$this->directory}/{$this->id}", $this->contentType);
        $this->assertEquals($event, $fileInfo->recordedEvents[0]);
    }
    
    function test_getFullyQualifiedFileName_returnPathToFile() {
        $expectedFileName = 
                DIRECTORY_SEPARATOR . "path" . 
                DIRECTORY_SEPARATOR . 'to' . 
                DIRECTORY_SEPARATOR . 'folder' . 
                DIRECTORY_SEPARATOR . 'file_name.pdf';
        $this->assertEquals($this->fileInfo->getFullyQualifiedFileName(), $expectedFileName);
    }
    function test_updateSize_changeSize() {
        $this->fileInfo->updateSize($size = 1.3);
        $this->assertEquals($size, $this->fileInfo->size);
    }
}

class TestableFileInfo extends FileInfo{
    public $id, $folders, $name, $size;
    public string $bucketName;
    public ?string $contentType;
    public string $objectName;
    public $recordedEvents;
}

