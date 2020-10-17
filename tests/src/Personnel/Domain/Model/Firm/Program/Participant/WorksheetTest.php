<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use Personnel\Domain\Model\Firm\Program\Participant;
use Tests\TestBase;

class WorksheetTest extends TestBase
{
    protected $worksheet;
    protected $participant;
    protected $programId = "programId";
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = new TestableWorksheet();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->worksheet->participant = $this->participant;
    }
    
    public function test_belongsToParticipantInProgram_returnParticipantProgramEqualsResult()
    {
        $this->participant->expects($this->once())
                ->method("programEquals")
                ->with($this->programId)
                ->willReturn(true);
        $this->assertTrue($this->worksheet->belongsToParticipantInProgram($this->programId));
    }
}

class TestableWorksheet extends Worksheet
{
    public $participant;
    public $id;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
