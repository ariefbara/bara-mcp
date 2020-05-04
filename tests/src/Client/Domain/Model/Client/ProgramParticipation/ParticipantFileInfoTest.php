<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation;
use Shared\Domain\Model\FileInfo;
use Tests\TestBase;

class ParticipantFileInfoTest extends TestBase
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
        $participantFileInfo  = new TestableParticipantFileInfo($this->programParticipation, $this->id, $this->fileInfo);
        $this->assertEquals($this->programParticipation, $participantFileInfo->programParticipation);
        $this->assertEquals($this->id, $participantFileInfo->id);
        $this->assertEquals($this->fileInfo, $participantFileInfo->fileInfo);
        $this->assertFalse($participantFileInfo->removed);
    }

}

class TestableParticipantFileInfo extends ParticipantFileInfo
{

    public $programParticipation;
    public $id;
    public $fileInfo;
    public $removed = false;

}
