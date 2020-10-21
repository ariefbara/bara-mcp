<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use SharedContext\Domain\Model\SharedEntity\ {
    FileInfo,
    FileInfoData
};
use Tests\TestBase;

class FirmFileInfoTest extends TestBase
{
    protected $firm;
    protected $fileInfoData;
    protected $fileInfo;
    protected $firmFileInfo;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
        
        $this->firmFileInfo = new TestableFirmFileInfo($this->firm, "id", $this->fileInfoData);
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->firmFileInfo->fileInfo = $this->fileInfo;
    }
    
    public function test_construct_setProperties()
    {
        $firmFileInfo = new TestableFirmFileInfo($this->firm, $this->id, $this->fileInfoData);
        $this->assertEquals($this->firm, $firmFileInfo->firm);
        $this->assertEquals($this->id, $firmFileInfo->id);
        $this->assertFalse($firmFileInfo->removed);
        
        $fileInfo = new FileInfo($this->id, $this->fileInfoData);
        $this->assertEquals($fileInfo, $firmFileInfo->fileInfo);
    }
    
    public function test_getFullyQualifiedFileName_returnFileInfoGetFullyQualifiedFileNameResult()
    {
        $this->fileInfo->expects($this->once())
                ->method("getFullyQualifiedFileName");
        $this->firmFileInfo->getFullyQualifiedFileName();
    }
}

class TestableFirmFileInfo extends FirmFileInfo
{
    public $firm;
    public $id;
    public $fileInfo;
    public $removed;
}
