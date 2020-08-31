<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;

class WorksheetTest extends TestBase
{
    protected $worksheet;
    protected $partipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = new TestableWorksheet();
        
        $this->partipant = $this->buildMockOfClass(Participant::class);
        $this->worksheet->participant = $this->partipant;
    }
    
    public function test_getFirmWhitelableInfo_returnParticipantsGetFirmWhitelableInfoResult()
    {
        $this->partipant->expects($this->once())
                ->method('getFirmWhitelableInfo')
                ->willReturn($firmWhitelableInfo = $this->buildMockOfClass(FirmWhitelableInfo::class));
        $this->assertEquals($firmWhitelableInfo, $this->worksheet->getFirmWhitelableInfo());
    }
    
    public function test_getParticipantName_returnParticipantsGetNameResult()
    {
        $this->partipant->expects($this->once())
                ->method('getName')
                ->willReturn($participantName = 'participant name');
        $this->assertEquals($participantName, $this->worksheet->getParticipantName());
    }
    
    public function test_getProgramId_returnParticipantsGetProgramIdResult()
    {
        $this->partipant->expects($this->once())
                ->method('getProgramId')
                ->willReturn($programId = 'programid');
        $this->assertEquals($programId, $this->worksheet->getProgramId());
    }
    
    public function test_getParticipantId_returnParticipantId()
    {
        $this->partipant->expects($this->once())
                ->method('getId')
                ->willReturn($participantId = 'participantId');
        $this->assertEquals($participantId, $this->worksheet->getParticipantId());
        
    }
}

class TestableWorksheet extends Worksheet
{
    public $participant;
    public $id;
    public $name;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
