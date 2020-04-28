<?php

namespace Client\Domain\Model\Firm\Program;

use Resources\Domain\ValueObject\DateInterval;
use Tests\TestBase;

class RegistrationPhaseTest extends TestBase
{
    public $registrationPhase;
    public $startEndDate;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationPhase = new TestableRegistrationPhase();
        $this->startEndDate = $this->buildMockOfClass(DateInterval::class);
        $this->registrationPhase->startEndDate = $this->startEndDate;
    }
    public function test_isOpen_returnStartEndDateContainCurrentTimeComparisonValue()
    {
        $this->startEndDate->expects($this->once())
            ->method('contain')
            ->willReturn(true);
        $this->assertTrue($this->registrationPhase->isOpen());
    }
}

class TestableRegistrationPhase extends RegistrationPhase
{
    public $program, $id, $name, $startEndDate, $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
