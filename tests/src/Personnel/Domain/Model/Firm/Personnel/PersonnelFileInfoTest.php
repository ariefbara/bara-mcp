<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\TestBase;

class PersonnelFileInfoTest extends TestBase
{
    protected $personnel;
    protected $fileInfo;
    protected $id = 'id';


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
    }
    
    public function test_construct_setProperties()
    {
        $personnelFileInfo = new TestablePersonnelFileInfo($this->personnel, $this->id, $this->fileInfo);
        $this->assertEquals($this->personnel, $personnelFileInfo->personnel);
        $this->assertEquals($this->id, $personnelFileInfo->id);
        $this->assertEquals($this->fileInfo, $personnelFileInfo->fileInfo);
        $this->assertFalse($personnelFileInfo->removed);
    }
}

class TestablePersonnelFileInfo extends PersonnelFileInfo
{

    public $personnel;
    public $id;
    public $fileInfo;
    public $removed;

}
