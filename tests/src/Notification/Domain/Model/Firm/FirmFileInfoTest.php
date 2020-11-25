<?php

namespace Notification\Domain\Model\Firm;

use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\TestBase;

class FirmFileInfoTest extends TestBase
{
    protected $firmFileInfo;
    protected $fileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmFileInfo = new TestableFirmFileInfo();
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->firmFileInfo->fileInfo = $this->fileInfo;
    }
    
    public function test_getFullyQualifiedName_returnFileInfoGetFullyQualifiedNameResult()
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
    
    function __construct()
    {
        parent::__construct();
    }
}
