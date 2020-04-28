<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation;
use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class ProgramParticipationFileInfoTest extends TestBase
{

    protected $programParticipation;
    protected $fileInfo;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
    }
    public function test_construct_setProperties()
    {
        $programParticipationFileInfo  = new TestableProgramParticipationFileInfo($this->programParticipation, $this->id, $this->fileInfo);
        $this->assertEquals($this->programParticipation, $programParticipationFileInfo->programParticipation);
        $this->assertEquals($this->id, $programParticipationFileInfo->id);
        $this->assertEquals($this->fileInfo, $programParticipationFileInfo->fileInfo);
        $this->assertFalse($programParticipationFileInfo->removed);
    }

}

class TestableProgramParticipationFileInfo extends ProgramParticipationFileInfo
{

    public $programParticipation;
    public $id;
    public $fileInfo;
    public $removed = false;

}
