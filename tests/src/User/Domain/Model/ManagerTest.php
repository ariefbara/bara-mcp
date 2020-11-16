<?php

namespace User\Domain\Model;

use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $manager;
    protected $managerFileInfoId = "managerFileInfoId", $fileInfoData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TestableManager();
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
    }
    
    protected function executeSaveFileInfo()
    {
        return $this->manager->saveFileInfo($this->managerFileInfoId, $this->fileInfoData);
    }
    public function test_saveFileInfo_returnManagerFileInfo()
    {
        $managerFileInfo = new Manager\ManagerFileInfo($this->manager, $this->managerFileInfoId, $this->fileInfoData);
        $this->assertEquals($managerFileInfo, $this->executeSaveFileInfo());
    }
    public function test_saveFileInfo_removedManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function (){
            $this->executeSaveFileInfo();
        };
        $errorDetail = "forbidden: only active manage can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableManager extends Manager
{
    public $firmId;
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
