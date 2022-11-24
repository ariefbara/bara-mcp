<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class ParticipantFileInfoTest extends TestBase
{
    protected $participant;
    protected $participantFileInfo, $fileInfo;
    protected $id = 'newId', $fileInfoData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->fileInfoData = new FileInfoData('filename.ext', 10000);
        
        $this->participantFileInfo = new TestableParticipantFileInfo($this->participant, 'id', $this->fileInfoData);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->participantFileInfo->fileInfo = $this->fileInfo;
    }
    
    protected function construct()
    {
        return new TestableParticipantFileInfo($this->participant, $this->id, $this->fileInfoData);
    }
    public function test_construct_setProperties()
    {
        $participantFileInfo = $this->construct();
        $this->assertSame($this->participant, $participantFileInfo->participant);
        $this->assertSame($this->id, $participantFileInfo->id);
        $this->assertInstanceOf(FileInfo::class, $participantFileInfo->fileInfo);
    }
    
    protected function getFullyQualifiedFileName()
    {
        return $this->participantFileInfo->getFullyQualifiedFileName();
    }
    public function test_getFullyQualifiedFileName_returnFileInfoGetFullyQualifiedFileNameResult()
    {
        $this->fileInfo->expects($this->once())
                ->method('getFullyQualifiedFileName')
                ->willReturn($fileName = 'fullyQualifiedFilename.jpg');
        $this->assertSame($fileName, $this->getFullyQualifiedFileName());
    }
    
    //
    protected function assertUsableByParticipant()
    {
        $this->participantFileInfo->assertUsableByParticipant($this->participant);
    }
    public function test_assertUsableByParticipant_differentParticipant_forbidden()
    {
        $this->participantFileInfo->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertUsableByParticipant();
        }, 'Forbidden', 'unusable participant file, can only use own file');
    }
    public function test_assertUsableByParticipant_sameParticipant_void()
    {
        $this->assertUsableByParticipant();
        $this->markAsSuccess();
    }
}

class TestableParticipantFileInfo extends ParticipantFileInfo
{
    public $participant;
    public $id;
    public $fileInfo;
}
